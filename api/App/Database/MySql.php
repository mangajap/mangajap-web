<?php

namespace App\Database;

use App\Database;
use App\HTTP;
use App\JsonApi\Document\Errors;
use App\JsonApi\JsonApiException;
use PDO;
use PDOException;

class MySql extends Database {

    private $pdo;

    public function __construct($descriptor) {
        $this->connect($descriptor);

        return $this;
    }

    private function connect($descriptor) {
        $host = $descriptor['host'];
        $dbname = $descriptor['dbname'];
        $username = $descriptor['username'];
        $password = $descriptor['password'];
        $options = $descriptor['options'] ?? [];

        if (!isset($options[PDO::ATTR_ERRMODE]))
            $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

        $this->pdo = new PDO(
            "mysql:host=" . $host . ";dbname=" . $dbname,
            $username,
            $password,
            $options
        );

        $this->pdo->query("SET NAMES UTF8");

        return true;
    }

    private function getPDO(): PDO {
        return $this->pdo;
    }



    public function execute($sqlStatement, $bindParams = [], $bindTypes = []) {
        $statement = $this->getPDO()->prepare($sqlStatement);

        foreach ($bindParams as $field => $value) {
            $statement->bindValue(
                ':'.$field,
                $value,
                Column::toPDO($bindTypes[$field] ?? "")
            );
        }

//        var_dump($sqlStatement);
//        print_r($bindParams);
//        print_r($bindTypes);

        try {
            return $statement->execute();
        } catch (PDOException $e) {
            throw new Errors(
                new JsonApiException(
                    null,
                    null,
                    HTTP::CODE_BAD_REQUEST,
                    null,
                    "Invalid statement SQL",
                    $e->getMessage(),
                    null,
                    null,
                    $e->getTrace()
                )
            );
        }
    }

    public function query($sqlStatement, $bindParams = [], $bindTypes = []) {
        $statement = $this->getPDO()->prepare($sqlStatement);

        foreach ($bindParams as $field => $value) {
            $statement->bindValue(
                ':'.$field,
                $value,
                Column::toPDO($bindTypes[$field] ?? "")
            );
        }

//        var_dump($sqlStatement);
//        print_r($bindParams);
//        print_r($bindTypes);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Errors(
                new JsonApiException(
                    null,
                    null,
                    HTTP::CODE_BAD_REQUEST,
                    null,
                    "Invalid statement SQL",
                    $e->getMessage(),
                    null,
                    null,
                    $e->getTrace()
                )
            );
        }
    }

    public function insert($table, array $data, $bindTypes = []) {
        $fieldsClause = [];
        $valuesClause = [];

        foreach ($data as $field => $value) {
            if (is_int($field) && empty($fieldsClause)) {
                $valuesClause[] = ':'.$field;
            }
            else {
                $fieldsClause[] = $field;
                $valuesClause[] = ':'.$field;
            }
        }

        if (empty($fieldsClause)) {
            $sqlStatement = "
                    INSERT INTO ". $table ."
                    VALUES(
                        " . implode(', ', $valuesClause) . "
                    );";
        }
        else {
            $sqlStatement = "
                    INSERT INTO ". $table ."(
                        " . implode(', ', $fieldsClause) . "
                    )
                    VALUES(
                        " . implode(', ', $valuesClause) . "
                    );";
        }

        $statement = $this->getPDO()->prepare($sqlStatement);

        foreach ($data as $field => $value) {
            $statement->bindValue(
                ':'.$field,
                $value,
                Column::toPDO($bindTypes[$field] ?? "")
            );
        }

//        var_dump($sqlStatement);
//        var_dump($data);
//        return true;

        try {
            return $statement->execute();
        } catch (PDOException $e) {
            throw new Errors(
                new JsonApiException(
                    null,
                    null,
                    HTTP::CODE_BAD_REQUEST,
                    null,
                    "Invalid statement SQL",
                    $e->getMessage(),
                    null,
                    null,
                    $e->getTrace()
                )
            );
        }
    }

    public function update($table, $data, $conditions = [], $bindTypes = []) {
        $bindParams = [];

        $setClause = [];
        $whereClause = [];

        foreach ($data as $field => $value) {
            if ($value === null) {
                $setClause[] = $field . " = NULL";
            }
            else {
                $setClause[] = $field . " = " . ':'.$field;
                $bindParams[$field] = $value;
            }
        }
        foreach ($conditions as $field => $value) {
            $whereClause[] = $field . " = " . ':'.$field;

            $bindParams[$field] = $value;
        }

        if (empty($whereClause)) {
            $sqlStatement = "
                UPDATE
                    " . $table . "
                SET
                    " . implode(', ', $setClause) . ";";
        }
        else {
            $sqlStatement = "
                UPDATE
                    " . $table . "
                SET
                    " . implode(', ', $setClause) . "
                WHERE
                    " . implode(' AND ', $whereClause) . ";";
        }

        $statement = $this->getPDO()->prepare($sqlStatement);

        foreach ($bindParams as $field => $value) {
            $statement->bindValue(
                ':'.$field,
                $value,
                Column::toPDO($bindTypes[$field] ?? "")
            );
        }

//        var_dump($sqlStatement);
//        var_dump($data);
//        print_r($bindParams);
//        return true;

        try {
            return $statement->execute();
        } catch (PDOException $e) {
            throw new Errors(
                new JsonApiException(
                    null,
                    null,
                    HTTP::CODE_BAD_REQUEST,
                    null,
                    "Invalid statement SQL",
                    $e->getMessage(),
                    null,
                    null,
                    $e->getTrace()
                )
            );
        }
    }

    public function delete($table, $conditions = null, $bindParams = [], $bindTypes = []) {
        if (is_array($conditions)) {
            $sqlStatement = "
                DELETE FROM " . $table . "
                WHERE
                    " . implode(' AND ', $conditions) . "
            ";
        }
        elseif (is_string($conditions)) {
            $sqlStatement = "
                DELETE FROM " . $table . "
                WHERE
                    " . $conditions. "
            ";
        }
        else {
            $sqlStatement = "
                DELETE FROM " . $table . "
            ";
        }


        $statement = $this->getPDO()->prepare($sqlStatement);

        foreach ($bindParams as $field => $value) {
            $statement->bindValue(
                ':'.$field,
                $value,
                Column::toPDO($bindTypes[$field] ?? "")
            );
        }

//        var_dump($sqlStatement);
//        var_dump($bindParams);
//        var_dump($bindTypes);
//        return true;

        try {
            return $statement->execute();
        } catch (PDOException $e) {
            throw new Errors(
                new JsonApiException(
                    null,
                    null,
                    HTTP::CODE_BAD_REQUEST,
                    null,
                    "Invalid statement SQL",
                    $e->getMessage(),
                    null,
                    null,
                    $e->getTrace()
                )
            );
        }
    }


    public function lastInsertId($sequenceName = null): string {
        return $this->getPDO()->lastInsertId($sequenceName);
    }


    public function close() {
        $this->pdo = null;

        return true;
    }
}