<?php

namespace Models;

use PDO;

/**
 * Class Model
 *
 * Generic class for Model
 * Contain basic function and connection to the database
 *
 * @package Models
 */
class Model
{

    /**
     * @var PDO
     */
    private static PDO $database;

	/**
	 * Establish and set the database connection
	 *
	 * @return void
	 */
    private static function setDatabase(): void {
        self::$database = new PDO('mysql:host=' . DB_HOST . '; dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        //self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

	/**
	 * Configures the PDO database connection for a viewer-specific database.
	 *
	 * @return void No return value.
	 */
    private static function setDatabaseViewer(): void {
        self::$database = new PDO('mysql:host=' . DB_HOST_VIEWER . '; dbname=' . DB_NAME_VIEWER, DB_USER_VIEWER, DB_PASSWORD_VIEWER);
        //self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

	/**
	 * Retrieves the PDO database connection instance.
	 *
	 * @return PDO The database connection instance.
	 */
    protected function getDatabase(): PDO {
        self::setDatabase();
        return self::$database;
    }

	/**
	 * Return the database viewer connection
	 *
	 * @return PDO
	 */
    protected function getDatabaseViewer(): PDO {
        self::setDatabaseViewer();
        return self::$database;
    }
}
