<?php

declare(strict_types=1);

class Database
{
    private string $host;
    private string $dbName;
    private string $dbUser;
    private string $dbPass;
    private ?PDO $connection = null;

    public function __construct(string $host, string $dbName, string $dbUser, string $dbPass)
    {
        $this->host = $host;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
    }

    public function connect(): PDO
    {
        if ($this->connection === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->dbUser, $this->dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return $this->connection;
    }
}