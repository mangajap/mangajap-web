<?php

namespace App;


abstract class Database {

    abstract public function execute($sqlStatement, $bindParams = [], $bindTypes = []);

    abstract public function query($sqlStatement, $bindParams = [], $bindTypes = []);

    abstract public function insert($table, array $data, $bindTypes = null);

    abstract public function update($table, $data, $conditions = [], $bindTypes = []);

    abstract public function delete($table, $conditions = null, $bindParams = null, $bindTypes = null);

    abstract public function lastInsertId($sequenceName = null): string;
}