<?php

class UserDB
{
    private $dbFile = 'userDb.json';
    private $db = [];
    /** @var  UserDB */
    private static $instance;

    public static function getDB() {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * UserDB constructor.
     */
    public function __construct()
    {
        if (file_exists($this->dbFile)) {
            $str = file_get_contents($this->dbFile);
            $this->db = json_decode($str, true);
        }
    }

    public function __destruct()
    {
        if (!empty($this->db)) {
            file_put_contents($this->dbFile, json_encode($this->db, JSON_PRETTY_PRINT));
        }
    }

    public function getUser($username) {
        if (array_key_exists($username, $this->db)) {
            return $this->db[$username];
        }
        return null;
    }

    public function saveUser($username, $value) {
        $this->db[$username] = $value;
    }

    public function getUserToken($username) {
        if (array_key_exists($username, $this->db)) {
            return $this->db[$username]['access_token'];
        }
        return null;
    }


}