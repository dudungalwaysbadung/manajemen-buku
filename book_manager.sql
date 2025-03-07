CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    category ENUM('Fiksi', 'Non-Fiksi') NOT NULL DEFAULT 'Fiksi',
    image LONGBLOB NOT NULL
);