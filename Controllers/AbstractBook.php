<?php
namespace Controllers;

use PDO;
use Database\DBConnection;

// AbstractBook berisi koneksi database dan mendefinisikan metode abstrak untuk CRUD.
abstract class AbstractBook implements IBook {
    protected $conn; // Properti untuk menyimpan koneksi database

    public function __construct(DBConnection $db) {
        $this->conn = $db->connect(); // Menghubungkan ke database
    }

    // Metode-metode ini harus diimplementasikan oleh kelas turunan
    abstract public function getBooks(); 
    abstract public function addBook(string $title, string $author, int $year, string $category, $image );
    abstract public function updateBook(int $id, string $title, string $author, int $year, string $category, $image);
    abstract public function deleteBook(int $id);
}
