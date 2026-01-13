<?php
// Proses tambah gallery
if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $username = $_SESSION['username'];
    $tanggal = date('Y-m-d H:i:s');
    
    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "images/";
        $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $new_filename = 'gallery_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = $target_file;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO gallery (judul, deskripsi, gambar, tanggal, username) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $judul, $deskripsi, $gambar, $tanggal, $username);
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('Gallery berhasil ditambahkan!'); window.location='admin.php?page=gallery';</script>";
}

// Proses update gallery
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    
    // Cek apakah ada gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        // Hapus gambar lama
        $sql_old = "SELECT gambar FROM gallery WHERE id = ?";
        $stmt_old = $conn->prepare($sql_old);
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $result_old = $stmt_old->get_result();
        $row_old = $result_old->fetch_assoc();
        if ($row_old && file_exists($row_old['gambar'])) {
            unlink($row_old['gambar']);
        }
        $stmt_old->close();
        
        // Upload gambar baru
        $target_dir = "images/";
        $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $new_filename = 'gallery_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE gallery SET judul=?, deskripsi=?, gambar=? WHERE id=?");
            $stmt->bind_param("sssi", $judul, $deskripsi, $target_file, $id);
        }
    } else {
        $stmt = $conn->prepare("UPDATE gallery SET judul=?, deskripsi=? WHERE id=?");
        $stmt->bind_param("ssi", $judul, $deskripsi, $id);
    }
    
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('Gallery berhasil diupdate!'); window.location='admin.php?page=gallery';</script>";
}

// Proses hapus gallery
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Hapus gambar dari folder
    $sql = "SELECT gambar FROM gallery WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row && file_exists($row['gambar'])) {
        unlink($row['gambar']);
    }
    $stmt->close();
    
    // Hapus dari database
    $stmt = $conn->prepare("DELETE FROM gallery WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('Gallery berhasil dihapus!'); window.location='admin.php?page=gallery';</script>";
}

// Pagination
$limit = 4;
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$start = ($page - 1) * $limit;

$sql_count = "SELECT COUNT(*) as total FROM gallery";
$result_count = $conn->query($sql_count);
$total_records = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
?>

<div class="container">
    <!-- Tombol Tambah -->
    <div class="row mb-3">
        <div class="col">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Gallery
            </button>
        </div>
    </div>

    <!-- Tabel Gallery -->
    <div class="row">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th class="w-25">Judul</th>
                        <th class="w-50">Deskripsi</th>
                        <th class="w-25">Gambar</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM gallery ORDER BY tanggal DESC LIMIT $start, $limit";
                    $hasil = $conn->query($sql);
                    
                    $no = $start + 1;
                    while ($row = $hasil->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= $row["judul"] ?></strong>
                                <br><small>pada: <?= $row["tanggal"] ?></small>
                                <br><small>oleh: <?= $row["username"] ?></small>
                            </td>
                            <td><?= $row["deskripsi"] ?></td>
                            <td>
                                <?php if ($row["gambar"] != '' && file_exists($row["gambar"])) { ?>
                                    <img src="<?= $row["gambar"] ?>" width="100" class="img-thumbnail">
                                <?php } ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit<?= $row['id'] ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <a href="admin.php?page=gallery&hapus=<?= $row['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Yakin ingin menghapus gallery ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                                
                                <!-- Modal Edit -->
                                <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Gallery</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Judul</label>
                                                        <input type="text" class="form-control" name="judul" 
                                                               value="<?= $row['judul'] ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea class="form-control" name="deskripsi" rows="3" required><?= $row['deskripsi'] ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Gambar (Kosongkan jika tidak ingin mengubah)</label>
                                                        <input type="file" class="form-control" name="gambar" accept="image/*">
                                                        <?php if ($row["gambar"] != '') { ?>
                                                            <img src="<?= $row["gambar"] ?>" width="100" class="mt-2 img-thumbnail">
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1) { ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="admin.php?page=gallery&halaman=<?= $page - 1 ?>">Previous</a>
            </li>
            
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?page=gallery&halaman=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php } ?>
            
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="admin.php?page=gallery&halaman=<?= $page + 1 ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php } ?>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="gambar" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
