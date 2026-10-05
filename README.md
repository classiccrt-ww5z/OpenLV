# OpenLV

A self-hostable version of **LiiVid**, a video-sharing site with channels, playlists,
subscriptions, groups, private messages and an admin panel. PHP and MySQL.

LiiVid was copy.floppy's own project - a fork of [VidLii](https://www.vidlii.com/)'s
source, restyled after 2008-era YouTube, launched late 2025. It went offline after a
backdoor gave everyone admin access. This is that same source with the vulnerabilities
and bugs fixed, so it's mostly unchanged - see
[the Altipedia page](https://altipedia.fandom.com/wiki/LiiVid_(copy.floppy)) for the
history.

> **Please read this before using anything here.**
>
> copy.floppy does not endorse this, but he was fine with me doing it.
>
> I don't recommend using this source code for anything. You can read it for research
> and host it. I wouldn't recommend forking it to run your own video sharing site.

## Install

```bash
git clone <repo> openlv && cd openlv
./install.sh
docker compose up -d
```

Then open http://localhost:8888 and log in with the admin username and password
`install.sh` printed. It writes `.env` with random secrets, creates the database,
imports the schema, makes an admin and sets permissions. Re-running it is safe.

You need PHP 8.2+, MariaDB/MySQL and ffmpeg. The one setting people miss:
**`short_open_tag` must be `On`** in `php.ini`, otherwise the templates render nothing.
`install.sh` doesn't run composer — do `composer install --working-dir=app` if you're
not using the prebuilt image.

The source is in `app/`, which is the document root. Everything else in this repo sits
outside it and is never served.

## Credits

Modified and maintained by ww5z. LiiVid by copy.floppy, forked from
[VidLii](https://www.vidlii.com/). GUMP validation by Jeremy Gordon. PHPMailer and
Gregwar Captcha are bundled under `app/vendor/`.
