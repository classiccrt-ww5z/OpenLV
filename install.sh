#!/usr/bin/env bash
#
# OpenLV installer.
#
#   ./install.sh
#
# Writes .env with fresh secrets, creates the database, imports the schema, makes an
# admin account and fixes directory permissions. Then: docker compose up -d
#
# Safe to re-run. An existing .env is left alone and an existing admin is not modified.
#
#   --root-password PASS   MariaDB/MySQL root password (skips the prompt)
#   --admin-password PASS  default: random, printed at the end
#   --skip-db              only write .env and fix permissions

set -euo pipefail
cd "$(dirname "$0")"

DB_NAME="${DB_NAME:-openlv}"
DB_USER="${DB_USER:-openlv}"
ROOT_PASSWORD=""
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASSWORD=""
SKIP_DB=0

while [ $# -gt 0 ]; do
    case "$1" in
        --root-password)   ROOT_PASSWORD="${2:-}"; shift 2 ;;
        --admin-password)  ADMIN_PASSWORD="${2:-}"; shift 2 ;;
        --skip-db)         SKIP_DB=1; shift ;;
        -h|--help)         sed -n '2,15p' "$0" | sed 's/^# \{0,1\}//'; exit 0 ;;
        *) echo "unknown flag: $1" >&2; exit 1 ;;
    esac
done

say()  { printf '\n\033[1m%s\033[0m\n' "$*"; }
info() { printf '  %s\n' "$*"; }
die()  { printf '\nerror: %s\n' "$*" >&2; exit 1; }

command -v openssl >/dev/null || die "openssl not found"
[ -f .env.example ] || die ".env.example missing"
[ -f schema.sql ]   || die "schema.sql missing"
[ -d app ]         || die "app/ missing"

# ------------------------------------------------------------------ 1. config

say "1/4  config"

# The db service reads ROOT_PASSWORD from .env, so whatever is in there is what the
# container will actually be running with. That value has to win.
if [ -f .env ] && [ -n "$(grep '^ROOT_PASSWORD=' .env 2>/dev/null)" ]; then
    FILE_ROOT="$(grep '^ROOT_PASSWORD=' .env | cut -d= -f2-)"
    if [ -n "$ROOT_PASSWORD" ] && [ "$ROOT_PASSWORD" != "$FILE_ROOT" ]; then
        info "ignoring --root-password, .env already has a different one"
    fi
    ROOT_PASSWORD="$FILE_ROOT"
fi

if [ -f .env ]; then
    info ".env exists, keeping it"
else
    [ -n "$ROOT_PASSWORD" ]   || ROOT_PASSWORD="$(openssl rand -hex 16)"
    [ -n "$ADMIN_PASSWORD" ]  || ADMIN_PASSWORD="$(openssl rand -hex 6)"
    cat > .env <<EOF
DB_HOST=db
DB_DATABASE=$DB_NAME
DB_USER=$DB_USER
DB_PASSWORD=$(openssl rand -hex 16)
DB_CHARSET=utf8mb4
ROOT_PASSWORD=$ROOT_PASSWORD
WEB_PORT=8888

ADMIN_PASSWORD=$ADMIN_PASSWORD
ENCRYPTION_KEY=$(openssl rand -hex 32)
CONVERTER_KEY=$(openssl rand -hex 24)
APP_DEBUG=0
EOF
    chmod 600 .env
    info "wrote .env"
fi

[ -n "$ADMIN_PASSWORD" ] || ADMIN_PASSWORD="$(grep '^ADMIN_PASSWORD=' .env | cut -d= -f2-)"
info "admin password: $ADMIN_PASSWORD"

# ------------------------------------------------------------------ 2. database

say "2/4  database"

# Prefer talking to the database through docker compose, because that is the
# documented setup and the root account is usually not reachable from the host.
MODE=""
if [ "$SKIP_DB" = "1" ]; then
    info "skipped (--skip-db)"
elif command -v docker >/dev/null && docker compose version >/dev/null 2>&1; then
    MODE=compose
elif command -v mariadb >/dev/null; then
    MODE=local-mariadb
elif command -v mysql >/dev/null; then
    MODE=local-mysql
else
    info "skipped, no docker compose or mysql client here"
fi

if [ "$MODE" = "compose" ]; then
    if [ -z "$ROOT_PASSWORD" ]; then
        printf '  MariaDB root password: '
        read -rs ROOT_PASSWORD; printf '\n'
    fi
    db() { docker compose exec -T -e MYSQL_PWD="$ROOT_PASSWORD" db mariadb -u root "$@"; }

    info "starting the database, first run takes a minute"
    docker compose up -d db >/dev/null 2>&1 || die "could not start the db service"
    for _ in $(seq 1 60); do
        db -e "SELECT 1;" >/dev/null 2>&1 && break
        sleep 2
    done
    db -e "SELECT 1;" >/dev/null 2>&1 || die "database did not come up"
elif [ -n "$MODE" ]; then
    if [ -z "$ROOT_PASSWORD" ]; then
        printf '  MySQL root password: '
        read -rs ROOT_PASSWORD; printf '\n'
    fi
    db() { MYSQL_PWD="$ROOT_PASSWORD" "${MODE#local-}" -u root "$@"; }
fi

if [ -n "$MODE" ]; then
    db -e "
        CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`
            CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
        CREATE USER IF NOT EXISTS '$DB_USER'@'%' IDENTIFIED BY '$(grep '^DB_PASSWORD=' .env | cut -d= -f2-)';
        GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'%';
        FLUSH PRIVILEGES;" >/dev/null
    info "database ready"

    if [ -z "$(db -N -B -e "SHOW TABLES LIKE 'users';" "$DB_NAME")" ]; then
        db "$DB_NAME" < schema.sql
        info "schema imported"
    else
        info "schema already there"
    fi
fi

# ------------------------------------------------------------------ 3. admin

say "3/4  admin account"

php_run() {
    if command -v php >/dev/null 2>&1; then
        php "$@"
    elif [ "$MODE" = "compose" ]; then
        docker compose run --rm --no-deps -T web php "$@"
    else
        return 1
    fi
}

if [ "$SKIP_DB" = "1" ] || [ -z "$MODE" ]; then
    info "skipped"
elif ! php_run -r 'echo 1;' >/dev/null 2>&1; then
    info "skipped, no php available"
else
    HASH="$(php_run -r 'echo password_hash($argv[1], PASSWORD_DEFAULT);' "$ADMIN_PASSWORD")"
    HASH="${HASH//\\/\\\\}"
    HASH="${HASH//\$/\\$}"

    if [ "$(db -N -B -e "SELECT COUNT(*) FROM users WHERE username = '$ADMIN_USER';" "$DB_NAME")" = "1" ]; then
        info "user '$ADMIN_USER' already exists, leaving it"
    else
        db "$DB_NAME" -e "
            INSERT INTO users
                (username, displayname, email, password, reg_date, last_login,
                 birthday, \`1st_latest_ip\`, country, activated, is_admin, is_mod)
            VALUES ('$ADMIN_USER','$ADMIN_USER','$ADMIN_USER@localhost','$HASH',
                    NOW(),NOW(),'2000-01-01','127.0.0.1','US',1,1,1);" >/dev/null
        info "created '$ADMIN_USER'"
    fi
fi

# ------------------------------------------------------------------ 4. perms

say "4/4  permissions"

DIRS="app/usfi/conv app/usfi/conv_2 app/usfi/v app/usfi/thmp app/usfi/prvw \
      app/usfi/tmp app/usfi/img app/usfi/avi app/usfi/avt app/usfi/bg \
      app/usfi/bner app/usfi/wbner app/usfi/css app/usfi/error_logs \
      app/cache app/tmp app/img app/_crons"

for d in $DIRS; do [ -d "$d" ] || mkdir -p "$d"; done

if command -v setfacl >/dev/null 2>&1; then
    for d in $DIRS; do
        setfacl -m u:33:rwx "$d" 2>/dev/null || true
        setfacl -d -m u:33:rwx "$d" 2>/dev/null || true
    done
    info "www-data (uid 33) can write"
else
    for d in $DIRS; do chmod 775 "$d" 2>/dev/null || true; done
    info "data dirs set to 775"
fi

# ------------------------------------------------------------------ done

say "done"

cat <<EOF
  admin user     : $ADMIN_USER
  admin password : $ADMIN_PASSWORD

  next:

    docker compose up -d
    open http://localhost:8888

  layout:

    app/     the site. this is the web root.
    root     Dockerfile, compose, install.sh, schema.sql, .env, README

EOF