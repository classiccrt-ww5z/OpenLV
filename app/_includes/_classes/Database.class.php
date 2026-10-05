<?php
    class Database {
        public      $RowNum;

        protected   $Connection;

        function __construct(bool $Show_Errors = false) {
            try {
                $this->Connection = new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE . ';charset=utf8mb4',
                    DB_USER,
                    DB_PASSWORD,
                    [
                        PDO::ATTR_ERRMODE            => $Show_Errors ? PDO::ERRMODE_EXCEPTION : PDO        ::ERRMODE_SILENT,
                        PDO::ATTR_EMULATE_PREPARES   => false,     
                        PDO::ATTR_PERSISTENT         => false,     
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );


            } catch (PDOException $e) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable', true, 503);
                die("<center>Database Error</center>");
            }
        }

public function execute(string $SQL, bool $Single = false, array $Execute = []): array {
        try {
            $newExecute = [];
            $SQL = $this->bind_repeated_placeholders($SQL, $Execute, $newExecute);

            $Query = $this->Connection->prepare($SQL);
            $Query->execute($newExecute);

        } catch (PDOException $e) {
            $this->fail($e, $SQL, $newExecute ?? []);
        }
        
            $this->RowNum = $Query->rowCount();
        
            if ($this->RowNum == 0) {
                return [];
            } elseif ($Single) {
                return @$Query->fetch(PDO::FETCH_ASSOC);
            } else {
                return @$Query->fetchAll(PDO::FETCH_ASSOC);
            }
        }



        public function modify(string $SQL, array $Execute = []): bool {
            if ($Execute) {
                
                foreach ($Execute as $Key => $Value) {
                    $Execute[$Key] = str_ireplace("eval", "evaI", $Value);
                   
                    if (is_null($Value)) {
                    	$Execute[$Key] = 0;
                    }
                }
                
            }
            try {
            	$newExecute = [];
            	$SQL = $this->bind_repeated_placeholders($SQL, $Execute, $newExecute);
            	$Query = $this->Connection->prepare($SQL);
            	$Query->execute($newExecute);
            } catch (PDOException $e) { $this->fail($e, $SQL, $newExecute ?? []); }

            $this->RowNum = $Query->rowCount();

            if ($this->RowNum > 0) {
                return true;
            }
            return false;
        }

        // A placeholder may legitimately appear more than once in a statement, but PDO
        // cannot bind the same named parameter twice with emulation disabled. Rename each
        // repetition to a unique name and clone the value. Shared by execute() and
        // modify() so both behave identically.
        private function bind_repeated_placeholders(string $SQL, array $Execute, array &$Bound): string {
            foreach ($Execute as $placeholder => $value) {
                if (!preg_match_all('/' . preg_quote($placeholder, '/') . '\b/', $SQL, $matches)) {
                    continue;
                }

                $count = count($matches[0]);

                if ($count <= 1) {
                    $Bound[$placeholder] = $value;
                    continue;
                }

                for ($i = 0; $i < $count; $i++) {
                    $newPlaceholder = $placeholder . '_' . $i;
                    $SQL = preg_replace('/' . preg_quote($placeholder, '/') . '\b/', $newPlaceholder, $SQL, 1);
                    $Bound[$newPlaceholder] = $value;
                }
            }

            return $SQL;
        }

        // Never echo SQL or bound parameters back to the client: the bound values include
        // password hashes and e-mail addresses. Write the detail to the error log instead.
        private function fail(PDOException $e, string $SQL, array $Execute): void {
            error_log(sprintf(
                '[database] %s | SQL: %s | Params: %s',
                $e->getMessage(),
                $SQL,
                json_encode($Execute)
            ));

            if (!headers_sent()) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            }

            if (getenv('APP_DEBUG') === '1') {
                die("PDO Error: " . $e->getMessage() . "\nSQL: " . $SQL . "\nParams: " . json_encode($Execute));
            }

            die('<center>A database error occurred. The incident has been logged.</center>');
        }

        public function last_id() {
            return $this->Connection->lastInsertId();
        }
    }
