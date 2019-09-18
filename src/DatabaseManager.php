<?php


use MongoDB\Client;

class DatabaseManager
{
    /**
     * @return Client a mongodb client
     */
    public function databaseConnect() {
        try {
            $db = new MongoDB\Client(getenv('DB_CONNECTION_STRING'));
            return $db;
        } catch (Exception $e) {
            echo 'could not connect to db because ' . $e ;
        }
    }
}