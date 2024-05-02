<?php 
include 'Library.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Library</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .library-container {
            background-color: #d7e3fc; 
            padding: 20px; 
        }
        .container {
            background-color: #edf2fb;    
        }
        .book-card {
            margin-bottom: 20px;
        }
        .borrowed-book {
            margin-bottom: 15px;
            margin-top: 30px;
        }
        h1 {
            font-size: 36px; /* Ukuran font */
            font-weight: bold; /* Membuat teks menjadi bold */
            margin-top: 40px; /* Jarak atas */
            margin-bottom: 40px; /* Jarak bawah */
            text-align: right;
            color: #1f3864;
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="container library-container">My Library</h1>

    <div class="row mt-4">
        <div class="col-md-6">
            <form action="" method="GET" class="form-inline">
                <input type="text" name="keyword" class="form-control mr-2" placeholder="Search...">
                <select name="sort" class="form-control mr-2">
                    <option value="judul">Sort by Alphabet</option>
                    <option value="tahun">Sort by Year</option>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="col-md-6 text-right">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addBookModal">Add Book</button>
        </div>
    </div>

    <div class="row mt-4">
        <?php foreach ($filteredBooks as $book): ?>
            <div class="col-md-4 book-card">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $book->judul ?></h5>
                        <p class="card-text">Penulis: <?= $book->penulis ?></p>
                        <p class="card-text">Tahun Terbit: <?= $book->tahunTerbit ?></p>
                        <?php if ($book->getStatusPinjam()): ?>
                            <p class="card-text">Status: Sedang Dipinjam</p>
                        <?php endif; ?>
                        <?php if ($book instanceof ReferenceBook): ?>
                            <p class="card-text">ISBN: <?= $book->isbn ?></p>
                            <p class="card-text">Penerbit: <?= $book->penerbit ?></p>
                        <?php endif; ?>
                        <form action="" method="POST">
                            <?php if (!$book->getStatusPinjam()): ?>
                                <button type="submit" name="pinjam" value="<?= $book->judul ?>" class="btn btn-primary">Borrow</button>
                            <?php else: ?>
                                <button type="submit" name="kembalikan" value="<?= $book->judul ?>" class="btn btn-danger">Return</button>
                            <?php endif; ?>
                            <button type="submit" name="hapus" value="<?= $book->judul ?>" class="btn btn-secondary">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row borrowed-book mt-4">
        <div class="col-md-12">
            <h3>Borrowed Books:</h3>
            <div class="card-deck">
                <?php foreach ($library->getBukuDipinjam() as $book): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $book->judul ?></h5>
                            <p class="card-text">Penulis: <?= $book->penulis ?></p>
                            <p class="card-text">Tahun Terbit: <?= $book->tahunTerbit ?></p>
                            <p class="card-text">Status: Sedang Dipinjam</p>
                            <?php if ($book instanceof ReferenceBook): ?>
                                <p class="card-text">ISBN: <?= $book->isbn ?></p>
                                <p class="card-text">Penerbit: <?= $book->penerbit ?></p>
                            <?php endif; ?>
                            <form action="" method="POST">
                                <button type="submit" name="kembalikan" value="<?= $book->judul ?>" class="btn btn-danger">Return</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Book Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1" role="dialog" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="judul">Title</label>
                        <input type="text" class="form-control" id="judul" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label for="penulis">Author</label>
                        <input type="text" class="form-control" id="penulis" name="penulis" required>
                    </div>
                    <div class="form-group">
                        <label for="tahun">Year</label>
                        <input type="number" class="form-control" id="tahun" name="tahun" required>
                    </div>
                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn">
                    </div>
                    <div class="form-group">
                        <label for="penerbit">Publisher</label>
                        <input type="text" class="form-control" id="penerbit" name="penerbit">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="tambahkan">Add Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
