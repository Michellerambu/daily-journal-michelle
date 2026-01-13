<?php
include "koneksi.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Daily Journal</title>
    <link rel="icon" href="images/logo.jpg" type="logo" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <style>

        body.dark-theme {
            background-color: #121212 !important;
            color: #eeeeee !important;
        }
        body.dark-theme .navbar {
            background-color: #333333 !important;
        }
        body.dark-theme .navbar .nav-link,
        body.dark-theme .navbar-brand {
            color: #eeeeee !important;
        }
        body.dark-theme .card {
            background-color: #1e1e1e;
            color: #eeeeee;
            border-color: #444444;
        }
        body.dark-theme .bg-danger-subtle {
            background-color: #332222 !important;
        }
        body.dark-theme footer {
            background: #222;
            color: #eee;
        }
        .btn-theme {
            margin-left: 0.5rem;
        }
        #schedule .card {
            min-height: 180px;
            padding: 0.75rem;
        }
        #profile img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
        @media (max-width: 768px) {
            #profile .profile-name {
                text-align: center !important;
                margin-top: 0.5rem;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">My Daily Journal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto text-dark align-items-center">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#hero">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#article">Article</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#gallery">Gallery</a>
                </li>
                <!-- Menu Schedule -->
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="scrollToSection('schedule');return false;">Schedule</a>
                </li>
                <!-- Menu Profile -->
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="scrollToSection('profile');return false;">Profile</a>
                </li>
                <!-- Tombol Dark Theme -->
                <li class="nav-item">
                    <button class="btn btn-dark btn-theme" id="btn-dark" title="Dark Theme">
                        <i class="bi bi-moon-fill"></i> Dark
                    </button>
                </li>
                <!-- Tombol Light Theme -->
                <li class="nav-item">
                    <button class="btn btn-outline-dark btn-theme" id="btn-light" title="Light Theme">
                        <i class="bi bi-sun-fill"></i> Light
                    </button>
                </li>
                <!-- Tombol Login -->
                <li class="nav-item">
                    <a href="login.php" class="btn btn-danger btn-theme" title="Login">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section id="hero" class="text-center p-5 bg-danger-subtle text-sm-start">
    <div class="container">
        <div class="d-sm-flex flex-sm-row-reverse align-items-center">
            <img src="images/banner.jpg" alt="banner" width="300" class="img-fluid" />
            <div>
                <h1 class="fw-bold display-4">Create Memories, Save Memories, Everyday</h1>
                <h4 class="lead display-6">Mencatat semua kegiatan sehari-hari yang ada tanpa terkecuali</h4>
                <h6>
                    <span id="tanggal"></span>
                    <span id="jam"></span>
                </h6>
            </div>
        </div>
    </div>
</section>

<!-- article begin -->
<section id="article" class="text-center p-5">
  <div class="container">
    <h1 class="fw-bold display-4 pb-3">article</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
      <?php
      $sql = "SELECT * FROM article ORDER BY tanggal DESC";
      $hasil = $conn->query($sql); 

      while($row = $hasil->fetch_assoc()){
      ?>
        <div class="col">
          <div class="card h-100">
            <img src="<?= $row["gambar"]?>" class="card-img-top" alt="<?= $row["judul"]?>" style="height: 200px; object-fit: cover;" onerror="this.src='images/default.jpg';" />
            <div class="card-body">
              <h5 class="card-title"><?= $row["judul"]?></h5>
              <p class="card-text">
                <?= $row["isi"]?>
              </p>
            </div>
            <div class="card-footer">
              <small class="text-body-secondary">
                <?= $row["tanggal"]?>
              </small>
            </div>
          </div>
        </div>
        <?php
      }
      ?> 
    </div>
  </div>
</section>
<!-- article end -->

<section id="gallery" class="text-center p-5 bg-danger-subtle">
    <div class="container">
        <h1 class="fw-bold display-4 pb-3">Gallery</h1>
        <div id="carouselExample" class="carousel slide">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/gallery1.jpg" class="d-block w-100" alt="gallery1" />
                </div>
                <div class="carousel-item">
                    <img src="images/gallery2.jpg" class="d-block w-100" alt="gallery2" />
                </div>
                <div class="carousel-item">
                    <img src="images/gallery3.jpg" class="d-block w-100" alt="gallery3" />
                </div>
                <div class="carousel-item">
                    <img src="images/kegiatan4.jpg" class="d-block w-100" alt="gallery4" />
                </div>
                <div class="carousel-item">
                    <img src="images/kegiatan5.jpg" class="d-block w-100" alt="gallery5" />
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>

<!-- Section Schedule -->
<section id="schedule" class="p-5">
    <div class="container">
        <h1 class="text-center fw-bold pb-4">Jadwal Kuliah & Kegiatan Mahasiswa</h1>
        <div class="row justify-content-center g-3">
            <!-- Senin -->
            <div class="col-12 col-md-3">
                <div class="card border-primary text-center h-100" style="border-width: 2px;">
                    <div class="card-header bg-primary text-white fw-semibold">Senin</div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>08:40 - 10:20</strong><br />
                            Pendidikan Kewarganegaraan<br />
                            Ruang E.3
                        </p>
                        <p>
                            <strong>12:30 - 15:00</strong><br />
                            Kriptografi<br />
                            Ruang H.5.11
                        </p>
                    </div>
                </div>
            </div>
            <!-- Selasa -->
            <div class="col-12 col-md-3">
                <div class="card border-success text-center h-100" style="border-width: 2px;">
                    <div class="card-header bg-success text-white fw-semibold">Selasa</div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>08:40 - 10:20</strong><br />
                            Technopreneurship<br />
                            Ruang H.7.1
                        </p>
                    </div>
                </div>
            </div>
            <!-- Rabu -->
            <div class="col-12 col-md-3">
                <div class="card border-danger text-center h-100" style="border-width: 2px;">
                    <div class="card-header bg-danger text-white fw-semibold">Rabu</div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>07:00 - 09:30</strong><br />
                            Sistem Operasi<br />
                            Ruang H.4.9
                        </p>
                        <p>
                            <strong>12:30 - 15:00</strong><br />
                            Logika Informatika<br />
                            Ruang H.5.12
                        </p>
                    </div>
                </div>
            </div>
            <!-- Kamis -->
            <div class="col-12 col-md-3">
                <div class="card border-warning text-center h-100" style="border-width: 2px;">
                    <div class="card-header bg-warning text-dark fw-semibold">Kamis</div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>07:00 - 08:40</strong><br />
                            Basis Data Praktek<br />
                            Ruang D.2.K
                        </p>
                        <p>
                            <strong>08:40 - 10:20</strong><br />
                            Pemrograman Berbasis Web<br />
                            Ruang D.2.J
                        </p>
                    </div>
                </div>
            </div>
            <!-- Jumat -->
            <div class="col-12 col-md-3">
                <div class="card border-info text-center h-100" style="border-width: 2px;">
                    <div class="card-header bg-info text-white fw-semibold">Jumat</div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>07:00 - 08:40</strong><br />
                            Basis Data Teori<br />
                            Ruang H.5.4
                        </p>
                        <p>
                            <strong>12:30 - 15:00</strong><br />
                            Rekayasa Perangkat Lunak<br />
                            Ruang H.3.9
                        </p>
                    </div>
                </div>
            </div>
            <!-- Sabtu -->
             <div class="col-12 col-md-3">
                <div class="card border-dark text-center h-100" style="border-width: 2px;">
                    <div class="card-header bg-dark text-white fw-semibold">Sabtu</div>
                    <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 150px;">
                        <p class="mb-0">Tidak Ada Jadwal</p>
                    </div>
                </div>
            </div>
            <!-- Minggu -->
             <div class="col-12 col-md-3">
                <div class="card border-secondary text-center h-100" style="border-width: 2px;">
                    <div class="card-header bg-secondary text-white fw-semibold">Minggu</div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>08:00 - 10:00</strong><br />
                            Ibadah<br />
                            Gereja
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Profile -->
<section id="profile" class="p-5 bg-light">
    <div class="container text-center" style="max-width: 700px;">
        <h1 class="fw-bold pb-4" style="font-size: 2rem;">Profil Mahasiswa</h1>
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-4">
            <div>
                <img src="images/profile.jpg" alt="Foto Mahasiswa" style="width: 140px; height: 140px; object-fit: cover; border-radius: 50%;" />
            </div>
            <div class="text-start" style="min-width: 320px;">
                <h3 style="font-weight: 600; margin-bottom: 1rem;">Michelle Landudjama</h3>
                <table style="width: 100%; font-size: 1rem; border-collapse: separate; border-spacing: 0 10px;">
                    <tbody>
                        <tr>
                            <td style="width: 35%; font-weight: 600;">NIM</td>
                            <td style="width: 5%; font-weight: 600;">:</td>
                            <td>A11.2024.15821</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Program Studi</td>
                            <td style="font-weight: 600;">:</td>
                            <td>Teknik Informatika</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Email</td>
                            <td style="font-weight: 600;">:</td>
                            <td>111202415821@mhs.dinus.ac.id</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Telepon</td>
                            <td style="font-weight: 600;">:</td>
                            <td>+62 823 4090 7013</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Alamat</td>
                            <td style="font-weight: 600;">:</td>
                            <td>Jl. Imam Bonjol No. 123, Semarang</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<footer class="text-center pt-4 pb-4">
    <div class="mb-3"> 
        <a href="https://instagram.com" class="h2 p-2 text-dark"><i class="bi bi-instagram"></i></a>
        <a href="https://twitter.com" class="h2 p-2 text-dark"><i class="bi bi-twitter"></i></a>
        <a href="https://wa.me" class="h2 p-2 text-dark"><i class="bi bi-whatsapp"></i></a>
    </div>
    <div>
        <p> Michelle Rambu Wani Landudjama &copy; 2025 </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function updateDateTime() {
        const now = new Date();
        
        // Array nama hari dan bulan dalam bahasa Indonesia
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        // Format tanggal: Hari, Tanggal Bulan Tahun
        const day = days[now.getDay()];
        const date = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        const formattedDate = `${day}, ${date} ${month} ${year}`;
        
        // Format jam: HH:MM:SS
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const formattedTime = `${hours}:${minutes}:${seconds}`;
        
        document.getElementById('tanggal').textContent = formattedDate + ' ';
        document.getElementById('jam').textContent = formattedTime;
    }
    
    // Update setiap detik
    setInterval(updateDateTime, 1000);
    
    // Panggil sekali saat load
    updateDateTime();

    // Fungsi scroll ke section tertentu
    function scrollToSection(id) {
        const elem = document.getElementById(id);
        if (elem) {
            elem.scrollIntoView({behavior: 'smooth'});
        }
    }

    // Theme switcher
    const btnDark = document.getElementById('btn-dark');
    const btnLight = document.getElementById('btn-light');

    btnDark.addEventListener('click', () => {
        document.body.classList.add('dark-theme');
        btnDark.classList.remove('btn-outline-dark');
        btnDark.classList.add('btn-dark');
        btnLight.classList.remove('btn-light');
        btnLight.classList.add('btn-outline-light');
    });

    btnLight.addEventListener('click', () => {
        document.body.classList.remove('dark-theme');
        btnLight.classList.remove('btn-outline-light');
        btnLight.classList.add('btn-outline-dark');
        btnDark.classList.remove('btn-dark');
        btnDark.classList.add('btn-outline-dark');
    });
</script>
</body>
</html>