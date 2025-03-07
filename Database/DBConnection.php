<?php
namespace Database;

use PDO;
use PDOException;

// Kelas DBConnection digunakan untuk menghubungkan aplikasi dengan database
class DBConnection {
    private $host = "localhost"; // Host database
    private $dbname = "book_manager"; // Nama database
    private $username = "root"; // Username database
    private $password = ""; // Password database (kosong untuk localhost)
    private $conn; // Properti untuk menyimpan koneksi database

     // Metode untuk membuat koneksi ke database
    public function connect() {
        try {
            // Membuat koneksi menggunakan PDO
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Mengaktifkan mode error exception
            return $this->conn; // Mengembalikan objek koneksi database
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage()); // Jika gagal, tampilkan pesan error
        }
    }
}
