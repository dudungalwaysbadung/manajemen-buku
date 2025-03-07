<?php
namespace Controllers;

use PDO;
use Database\DBConnection;

// BookController adalah implementasi dari AbstractBook yang menangani operasi CRUD buku
class BookController extends AbstractBook {
    
    // Mengambil semua data buku
    public function getBooks() {
        $stmt = $this->conn->query("SELECT * FROM books ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menambahkan buku baru ke database
    public function addBook(string $title, string $author, int $year, string $category, $image) {
        $stmt = $this->conn->prepare("INSERT INTO books (title, author, year, category, image) VALUES (:title, :author, :year, :category, :image)");
        $stmt->execute([
            'title' => $title,
            'author' => $author,
            'year' => $year,
            'category' => $category,
            'image' => file_get_contents($image["tmp_name"])
        ]);
    }

    // Memperbarui data buku berdasarkan ID
    public function updateBook(int $id, string $title, string $author, int $year, string $category, $image) {
        $stmt = $this->conn->prepare("UPDATE books SET title = :title, author = :author, year = :year, category = :category, image = :image WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'title' => $title,
            'author' => $author,
            'year' => $year,
            'category' => $category,
            'image' => file_get_contents($image["tmp_name"])
        ]);
    }

    // Menghapus buku dari database berdasarkan ID
    public function deleteBook(int $id) {
        $stmt = $this->conn->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    // Mencari buku berdasarkan judul atau kategori
    public function searchBooks($keyword) {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE title LIKE :keyword OR category LIKE :keyword ORDER BY id DESC");
        $stmt->execute(['keyword' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
