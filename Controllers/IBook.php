<?php
namespace Controllers;

// Interface IBook mendefinisikan kontrak untuk operasi CRUD pada buku.
interface IBook {
    public function getBooks(); // Metode untuk mengambil daftar buku dari database
    public function addBook(string $title, string $author, int $year, string $category, $image); // Metode untuk menambahkan buku baru ke dalam database
    public function updateBook(int $id, string $title, string $author, int $year, string $category, $image); // Metode untuk memperbarui data buku berdasarkan ID
    public function deleteBook(int $id);  // Metode untuk menghapus buku berdasarkan ID
}