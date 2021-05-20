<?php

namespace App\Database;

use PDO;

class Column {

    const TYPE_TINYINT = 0;
    const TYPE_SMALLINT = 1;
    const TYPE_MEDIUMINT = 2;
    const TYPE_INT = 3;
    const TYPE_BIGINT = 4;

    const TYPE_DECIMAL = 5;
    const TYPE_FLOAT = 6;
    const TYPE_DOUBLE = 7;
    const TYPE_REAL = 8;

    const TYPE_BIT = 9;
    const TYPE_BOOLEAN = 10;
    const TYPE_SERIAL = 11;

    const TYPE_DATE = 12;
    const TYPE_DATETIME = 13;
    const TYPE_TIMESTAMP = 14;
    const TYPE_TIME = 15;
    const TYPE_YEAR = 16;

    const TYPE_CHAR = 17;
    const TYPE_VARCHAR = 18;

    const TYPE_TINYTEXT = 19;
    const TYPE_TEXT = 20;
    const TYPE_MEDIUMTEXT = 21;
    const TYPE_LONGTEXT = 22;

    const TYPE_BINARY = 23;
    const TYPE_VARBINARY = 24;

    const TYPE_TINYBLOB = 25;
    const TYPE_MEDIUMBLOB = 26;
    const TYPE_BLOB = 27;
    const TYPE_LONGBLOB = 28;

    const TYPE_ENUM = 29;
    const TYPE_SET = 30;


    public static function toPDO($column) {
        switch($column) {
            case Column::TYPE_BOOLEAN:
                return PDO::PARAM_INT;
                break;
        }

        return PDO::PARAM_STR;
    }
}