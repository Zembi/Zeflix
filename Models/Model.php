<?php

class Model {
    protected PDO $db;

    public function __construct(PDO $db_conn) {
        $this->db = $db_conn;
    }
}