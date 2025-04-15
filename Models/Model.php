<?php

namespace Models;

use \PDO;

class Model {
    protected PDO $db;

    public function __construct(PDO $db_conn) {
        $this->db = $db_conn;
    }

    public function setGreekTimezoneBeforeQuery(): void {
        $this->db->exec("SET time_zone = 'Europe/Athens'");
    }
}