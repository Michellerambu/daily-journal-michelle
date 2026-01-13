<?php
// Proses tambah user
if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Enkripsi password dengan md5
    
    // Upload foto user
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "images/user/";
        
        // Buat folder jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        $new_filename = $username . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $foto = $target_file;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO user (username, password, foto) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $foto);
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('User berhasil ditambahkan!'); window.location='admin.php?page=user';</script>";
}

// Proses update user
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    
    // Cek apakah password diubah
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        
        // Cek apakah ada foto baru
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            // Hapus foto lama
            $sql_old = "SELECT foto FROM user WHERE id = ?";
            $stmt_old = $conn->prepare($sql_old);
            $stmt_old->bind_param("i", $id);
            $stmt_old->execute();
            $result_old = $stmt_old->get_result();
            $row_old = $result_old->fetch_assoc();
            if ($row_old && file_exists($row_old['foto'])) {
                unlink($row_old['foto']);
            }
            $stmt_old->close();
            
            // Upload foto baru
            $target_dir = "images/user/";
            $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
            $new_filename = $username . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE user SET username=?, password=?, foto=? WHERE id=?");
                $stmt->bind_param("sssi", $username, $password, $target_file, $id);
            }
        } else {
            $stmt = $conn->prepare("UPDATE user SET username=?, password=? WHERE id=?");
            $stmt->bind_param("ssi", $username, $password, $id);
        }
    } else {
        // Password tidak diubah
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            // Hapus foto lama
            $sql_old = "SELECT foto FROM user WHERE id = ?";
            $stmt_old = $conn->prepare($sql_old);
            $stmt_old->bind_param("i", $id);
            $stmt_old->execute();
            $result_old = $stmt_old->get_result();
            $row_old = $result_old->fetch_assoc();
            if ($row_old && file_exists($row_old['foto'])) {
                unlink($row_old['foto']);
            }
            $stmt_old->close();
            
            // Upload foto baru
            $target_dir = "images/user/";
            $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
            $new_filename = $username . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE user SET username=?, foto=? WHERE id=?");
                $stmt->bind_param("ssi", $username, $target_file, $id);
            }
        } else {
            $stmt = $conn->prepare("UPDATE user SET username=? WHERE id=?");
            $stmt->bind_param("si", $username, $id);
        }
    }
    
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('User berhasil diupdate!'); window.location='admin.php?page=user';</script>";
}

// Proses hapus user
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Hapus foto dari folder
    $sql = "SELECT foto FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row && file_exists($row['foto'])) {
        unlink($row['foto']);
    }
    $stmt->close();
    
    // Hapus dari database
    $stmt = $conn->prepare("DELETE FROM user WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('User berhasil dihapus!'); window.location='admin.php?page=user';</script>";
}

// Pagination
$limit = 4;
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$start = ($page - 1) * $limit;

$sql_count = "SELECT COUNT(*) as total FROM user";
$result_count = $conn->query($sql_count);
$total_records = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
?>

<div class="container">
    <!-- Tombol Tambah -->
    <div class="row mb-3">
        <div class="col">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah User
            </button>
        </div>
    </div>

    <!-- Tabel User -->
    <div class="row">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM user ORDER BY id DESC LIMIT $start, $limit";
                    $hasil = $conn->query($sql);
                    
                    $no = $start + 1;
                    while ($row = $hasil->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= $row["username"] ?></strong></td>
                            <td>
                                <?php if ($row["foto"] != '' && file_exists($row["foto"])) { ?>
                                    <img src="<?= $row["foto"] ?>" width="80" height="80" class="img-thumbnail rounded-circle" style="object-fit: cover;">
                                <?php } else { ?>
                                    <i class="bi bi-person-circle" style="font-size: 60px;"></i>
                                <?php } ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit<?= $row['id'] ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <a href="admin.php?page=user&hapus=<?= $row['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Yakin ingin menghapus user ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                                
                                <!-- Modal Edit -->
                                <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Username</label>
                                                        <input type="text" class="form-control" name="username" 
                                                               value="<?= $row['username'] ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                                                        <input type="password" class="form-control" name="password" 
                                                               placeholder="Masukkan password baru">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Foto (Kosongkan jika tidak ingin mengubah)</label>
                                                        <input type="file" class="form-control" name="foto" accept="image/*">
                                                        <?php if ($row["foto"] != '' && file_exists($row["foto"])) { ?>
                                                            <img src="<?= $row["foto"] ?>" width="100" class="mt-2 img-thumbnail">
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
                <a class="page-link" href="admin.php?page=user&halaman=<?= $page - 1 ?>">Previous</a>
            </li>
            
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?page=user&halaman=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php } ?>
            
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="admin.php?page=user&halaman=<?= $page + 1 ?>">Next</a>
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
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
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
