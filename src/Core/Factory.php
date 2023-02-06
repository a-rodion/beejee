<?php
declare(strict_types=1);

namespace Rodion\TaskList\Core;

use PDO;
use Rodion\TaskList\Model\Mapper\TaskMapper;

/**
 *
 */
class Factory
{
    /** @var PDO */
    private static $pdo;

    private static function getPdo(): PDO
    {
        if (\is_null(self::$pdo)) {
            self::$pdo = new PDO('mysql:dbname=beejee_db;host=127.0.0.1;port=3306',
                'beejee_user',
                'beejee_pass'
            );
        }
        return self::$pdo;
    }

    public static function getTaskMapper(): TaskMapper
    {
        return new TaskMapper(self::getPdo());
    }
}
