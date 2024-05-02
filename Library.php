<?php

class Book {
    public $id;
    public $judul;
    public $penulis;
    public $tahunTerbit;
    public $isbn;
    public $penerbit;
    public $statusPinjam;
    public $tanggalKembali;

    public function __construct($id, $judul, $penulis, $tahunTerbit, $isbn, $penerbit) {
        $this->id = $id;
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->tahunTerbit = $tahunTerbit;
        $this->isbn = $isbn;
        $this->penerbit = $penerbit;
        $this->statusPinjam = false;
        $this->tanggalKembali = null;
    }

    public function getStatusPinjam() {
        return $this->statusPinjam;
    }

    public function pinjam() {
        $this->statusPinjam = true;
        $this->tanggalKembali = date('Y-m-d', strtotime("+7 days")); // Mengatur tanggal kembali setelah 7 hari
    }

    public function kembalikan() {
        $this->statusPinjam = false;
        $this->tanggalKembali = null;
    }

    public function getTanggalKembali() {
        return $this->tanggalKembali;
    }

    public function hitungDenda() {
        if ($this->tanggalKembali === null) {
            return 0; 
        }
        $tanggalKembali = strtotime($this->tanggalKembali);
        $tanggalSekarang = time();
        $selisih = $tanggalSekarang - $tanggalKembali;
        if ($selisih > 0) {
            $hariTerlambat = floor($selisih / (60 * 60 * 24)); 
            return $hariTerlambat * 7000; // Denda 7000/hari
        }
        return 0; 
    }

    public function displayInfo() {
        echo "Judul: " . $this->judul . ", Penulis: " . $this->penulis . 
        ", Tahun Terbit: " . $this->tahunTerbit . ", ISBN: " . $this->isbn . ", Penerbit: " . $this->penerbit;
    }
}


class ReferenceBook extends Book {
    public function __construct($id, $judul, $penulis, $tahunTerbit, $isbn, $penerbit) {
        parent::__construct($id, $judul, $penulis, $tahunTerbit, $isbn, $penerbit);
    }

    public function getISBN() {
        return $this->isbn;
    }

    public function getPenerbit() {
        return $this->penerbit;
    }
}

class Node {
    public $book;
    public $next;

    public function __construct($book) {
        $this->book = $book;
        $this->next = null;
    }
}

class Library {
    private $head;
    private $batasPeminjaman;
    private $peminjamanSekarang;

    public function __construct($batasPeminjaman = 3) {
        $this->head = null;
        $this->batasPeminjaman = $batasPeminjaman;
        $this->peminjamanSekarang = 0;
    }

    public function tambahBuku($book) {
        $node = new Node($book);
        if ($this->head === null) {
            $this->head = $node;
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $node;
        }
        echo "<script>alert('Buku berhasil ditambahkan.');</script>";
    }

    public function pinjamBuku($judul) {
        // Memeriksa apakah telah mencapai batas peminjaman
        if ($this->peminjamanSekarang >= $this->batasPeminjaman) {
            echo "<script>alert('Maaf, Anda telah mencapai batas peminjaman buku.');</script>";
            return;
        }

        $current = $this->head;
        while ($current !== null) {
            if ($current->book->judul === $judul) {
                if ($current->book->getStatusPinjam()) {
                    echo "<script>alert('Maaf, buku sedang tidak tersedia atau tidak dapat dipinjam saat ini');</script>";
                    return;
                }
                $current->book->pinjam();
                $this->peminjamanSekarang++;
                break;
            }
            $current = $current->next;
        }
    }

    public function kembalikanBuku($judul) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->book->judul === $judul) {
                $denda = $current->book->hitungDenda();
                if ($denda > 0) {
                    echo "<script>alert('Anda telat mengembalikan buku. Denda yang harus dibayar: $denda');</script>";
                } else {
                    echo "<script>alert('Buku berhasil dikembalikan.');</script>";
                }
                $current->book->kembalikan();
                $this->peminjamanSekarang--;
                break;
            }
            $current = $current->next;
        }
    }

    public function sortBooksByJudul() {
        $books = $this->getBooks();
        usort($books, function($a, $b) {
            return strcmp($a->judul, $b->judul);
        });
        return $books;
    }

    public function sortBooksByTahun() {
        $books = $this->getBooks();
        usort($books, function($a, $b) {
            return $a->tahunTerbit - $b->tahunTerbit;
        });
        return $books;
    }

    public function hapusBuku($judul) {
        if ($this->head === null) {
            return;
        }

        if ($this->head->book->judul === $judul) {
            $this->head = $this->head->next;
            return;
        }

        $current = $this->head;
        while ($current->next !== null) {
            if ($current->next->book->judul === $judul) {
                $current->next = $current->next->next;
                return;
            }
            $current = $current->next;
        }
    }

    public function getBooks() {
        $books = [];
        $current = $this->head;
        while ($current !== null) {
            $books[] = $current->book;
            $current = $current->next;
        }
        return $books;
    }

    public function getBukuDipinjam() {
        $borrowedBooks = [];
        $current = $this->head;
        while ($current !== null) {
            if ($current->book->getStatusPinjam()) {
                $borrowedBooks[] = $current->book;
            }
            $current = $current->next;
        }
        return $borrowedBooks;
    }

    public function getBatasPeminjaman() {
        return $this->batasPeminjaman;
    }

    public function getPeminjamanSekarang() {
        return $this->getPeminjamanSekarang();
    }

    public function searchBooks($keyword) {
        $filteredBooks = [];
        $current = $this->head;
        while ($current !== null) {
            if (stripos($current->book->judul, $keyword) !== false || 
            stripos($current->book->penulis, $keyword) !== false) {
                $filteredBooks[] = $current->book;
            }
            $current = $current->next;
        }
        return $filteredBooks;
    }
}

$library = new Library();

// Display buku yang tersedia 
$library->tambahBuku(new Book(1,"The Power of Language", "Shin Do-hyun & Yoon Na ru", "2020", "978-623-7351-34-4", "Penerbit Haru"));
$library->tambahBuku(new Book(2,"Potongan Tubuh", "Pyun Hye-young & Park Min Gyu, dkk", "2019", "978-602-6486-32-5", "Penerbit Baca"));
$library->tambahBuku(new Book(3,"Siapa yang Datang ke Pemakamanku saat Aku Mati Nanti", "Kim Sang-hyun", "2023", "978-623-7351-54-2", "Penerbit Haru"));
$library->tambahBuku(new Book(4,"The Life-changing magic of tidying up", "Marie Kondo", "2019", "978-602-291-244-6", "Penerbit Bentang"));
$library->tambahBuku(new Book(5,"Map of the Soul: 7", "Murray Stein", "2021","978-602-6682-72-7", "Penerbit Haru"));
$library->tambahBuku(new Book(6,"Demian", "Herman Hesse", "2019", "978-602-60332-7-7", "SEMICOLON PUBLISHER"));
$library->tambahBuku(new ReferenceBook(7,"Tentang Tirani", "Timothy Synder", "2020", "978-602-03-7973-9", "PT Gramedia"));

// buku yang sedang dipinjam
$library->pinjamBuku("The Power of Language");
$library->pinjamBuku("Potongan Tubuh");
$library->pinjamBuku("Siapa yang Datang ke Pemakamanku saat Aku Mati Nanti");


//contoh logika penambaham buku
$library->tambahBuku(new Book(8, "Judul Buku Baru", "Penulis Buku Baru", "2024", "978-123-4567-89-0", "Penerbit Buku Baru"));

// variabel pencarian
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// logika pencarian
$filteredBooks = [];
if (!empty($keyword)) {
    $filteredBooks = $library->searchBooks($keyword);
} else {
    $filteredBooks = $library->getBooks();
}
// logika sorting
if ($sort === 'judul') {
    $filteredBooks = $library->sortBooksByJudul();
} elseif ($sort === 'tahun') {
    $filteredBooks = $library->sortBooksByTahun();
}

// logika hapus buku
if (isset($_POST['hapus'])) {
    $judulBuku = $_POST['hapus'];
    $library->hapusBuku($judulBuku);
    $filteredBooks = array_filter($filteredBooks, function ($book) use ($judulBuku) {
        return $book->judul !== $judulBuku;
    });
}

// logika pengembalian buku
if (isset($_POST['kembalikan'])) {
    $judulBuku = $_POST['kembalikan'];
    $library->kembalikanBuku($judulBuku);
}

?>