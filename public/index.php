<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../Database/DBConnection.php';
require_once __DIR__ . '/../Controllers/IBook.php';
require_once __DIR__ . '/../Controllers/AbstractBook.php';
require_once __DIR__ . '/../Controllers/BookController.php';
require_once __DIR__ . '/../tcpdf/tcpdf.php';

use Database\DBConnection;
use Controllers\BookController;

// Koneksi Database
$db = new DBConnection();
$bookController = new BookController($db);

// Ambil Data Buku
$search = $_GET['search'] ?? '';
$books = $search ? $bookController->searchBooks($search) : $bookController->getBooks();

// CRUD Operations
$editBook = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $bookController->addBook($_POST['title'], $_POST['author'], (int)$_POST['year'], $_POST['category'], $_FILES['image']);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['update'])) {
        $bookController->updateBook((int)$_POST['id'], $_POST['title'], $_POST['author'], (int)$_POST['year'], $_POST['category'], $_FILES['image']);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['delete'])) {
        $bookController->deleteBook((int)$_POST['id']);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['edit'])) {
        // Cari buku yang akan diedit dari daftar buku yang sudah diambil
        foreach ($books as $book) {
            if ($book['id'] == $_POST['id']) {
                $editBook = $book;
                break;
            }
        }

        // logic export PDF
    } elseif (isset($_POST['export_pdf'])) {
        // Ambil data sesuai pencarian
        $search = $_GET['search'] ?? '';
        $books = $search ? $bookController->searchBooks($search) : $bookController->getBooks();
    
        // Inisialisasi TCPDF
        $pdf = new TCPDF();
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->AddPage();
        // Set font
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Daftar Buku', 0, 1, 'C');
        // Header tabel
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(10, 10, 'ID', 1, 0, 'C');
        $pdf->Cell(70, 10, 'Judul', 1, 0, 'C');
        $pdf->Cell(50, 10, 'Penulis', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Tahun', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Kategori', 1, 1, 'C');
        // Isi tabel
        $pdf->SetFont('helvetica', '', 10);
        foreach ($books as $book) {
            $pdf->Cell(10, 10, $book['id'], 1, 0, 'C');
    
            // MultiCell untuk menangani teks panjang agar turun ke baris berikutnya
            $pdf->MultiCell(70, 10, $book['title'], 1, 'L', false, 0);
            $pdf->MultiCell(50, 10, $book['author'], 1, 'L', false, 0);
            $pdf->Cell(20, 10, $book['year'], 1, 0, 'C');
            $pdf->MultiCell(40, 10, $book['category'], 1, 'L', false, 1);
        }
        // Output PDF
        $pdf->Output('daftar_buku.pdf', 'D');
        exit;
    }
    
    
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Manajemen Buku</h2>    

        <!-- Form Tambah / Update Buku -->
        <div class="card p-4 mb-4">
            <h5 class="card-title"><?= $editBook ? 'Edit Buku' : 'Tambah Buku' ?></h5>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $editBook['id'] ?? '' ?>">
                <div class="mb-3">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" class="form-control" name="title" value="<?= $editBook['title'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" class="form-control" name="author" value="<?= $editBook['author'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" class="form-control" name="year" value="<?= $editBook['year'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-control" name="category" required>
                        <option value="Fiksi" <?= isset($editBook['category']) && $editBook['category'] === 'Fiksi' ? 'selected' : '' ?>>Fiksi</option>
                        <option value="Non-Fiksi" <?= isset($editBook['category']) && $editBook['category'] === 'Non-Fiksi' ? 'selected' : '' ?>>Non-Fiksi</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Gambar</label>
                    <input type="file" class="form-control" name="image" <?= isset($editBook) ? '' : 'required' ?>>
                    <?php if (isset($editBook) && !empty($editBook['image'])): ?>
                        <div class="mt-2">
                            <p>Gambar saat ini:</p>
                            <img src="data:image/jpeg;base64,<?= base64_encode($editBook['image']); ?>" width="100">
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" name="<?= $editBook ? 'update' : 'add' ?>">
                    <?= $editBook ? 'Update Buku' : 'Tambah Buku' ?>
                </button>
            </form>
        </div>

        <!-- Form Pencarian -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Cari buku..." value="<?= $_GET['search'] ?? '' ?>">
                <button type="submit" class="btn btn-secondary">Cari</button>
            </div>
        </form>

        <!-- Tabel Daftar Buku -->
        <div class="card p-4">
            <h5 class="card-title">Daftar Buku</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Tahun</th>
                        <th>Kategori</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= $book['id']; ?></td>
                        <td><?= htmlspecialchars($book['title']); ?></td>
                        <td><?= htmlspecialchars($book['author']); ?></td>
                        <td><?= $book['year']; ?></td>
                        <td><?= htmlspecialchars($book['category']); ?></td>
                        <td><img src="data:image/jpeg;base64,<?= base64_encode($book['image']); ?>" width="50"></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= $book['id']; ?>">
                                <input type="hidden" name="title" value="<?= $book['title']; ?>">
                                <input type="hidden" name="author" value="<?= $book['author']; ?>">
                                <input type="hidden" name="year" value="<?= $book['year']; ?>">
                                <input type="hidden" name="category" value="<?= $book['category']; ?>">
                                <button type="submit" class="btn btn-warning btn-sm" name="edit">Edit</button>
                            </form>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= $book['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" name="delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <div></div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Tombol Export PDF dan Logout -->
        <div class="d-flex justify-content-between  mt-3 mb-3">
            <form method="POST">
                <button type="submit" name="export_pdf" class="btn btn-primary">Export ke PDF</button>
            </form>
            <form method="POST" action="logout.php">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>

    </div>
</body>
</html>
