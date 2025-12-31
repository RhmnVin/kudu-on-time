<?php
// $conn = new mysqli("localhost", "root", "", "akademik_deadline");
// if ($conn->connect_error) {
//     die("Koneksi gagal");
// }

class Database {
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            self::$conn = new mysqli("localhost", "root", "", "akademik_deadline");
        }
        return self::$conn;
    }
}
