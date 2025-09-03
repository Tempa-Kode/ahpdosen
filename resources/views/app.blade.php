<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield("judul")</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- End fonts -->

    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset("vendors/core/core.css") }}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset("vendors/datatables.net-bs5/dataTables.bootstrap5.css") }}">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset("fonts/feather-font/css/iconfont.css") }}">
    <link rel="stylesheet" href="{{ asset("vendors/flag-icon-css/css/flag-icon.min.css") }}">
    <!-- endinject -->

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset("css/demo3/style.css") }}">
    <!-- End layout styles -->

    <link rel="shortcut icon" href="{{ asset("images/favicon.png") }}" />
</head>

<body>
    <div class="main-wrapper">

        <!-- partial:../../partials/_navbar.html -->
        <div class="horizontal-menu">
            <nav class="navbar top-navbar">
                <div class="container">
                    <div class="navbar-content">
                        <a href="#" class="navbar-brand">
                            AHP<span>DOSEN</span>
                        </a>
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img class="wd-30 ht-30 rounded-circle" src="{{ asset("images/profile.png") }}"
                                        alt="profile">
                                </a>
                                <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                                    <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                                        <div class="mb-3">
                                            <img class="wd-80 ht-80 rounded-circle"
                                                src="{{ asset("images/profile.png") }}" alt="">
                                        </div>
                                        <div class="text-center">
                                            <p class="tx-16 fw-bolder">{{ Auth::user()->nama }}</p>
                                            <p class="tx-12 text-muted">{{ Auth::user()->username }}</p>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled p-1">
                                        <li class="dropdown-item py-2">
                                            <form action="{{ route("logout") }}" method="post">
                                                @csrf
                                                @method("POST")
                                                <button type="submit" class="text-body ms-0 btn btn-danger w-100">
                                                    <i class="me-2 icon-md" data-feather="log-out"></i>
                                                    <span>Log Out</span>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                            data-toggle="horizontal-menu-toggle">
                            <i data-feather="menu"></i>
                        </button>
                    </div>
                </div>
            </nav>
            <nav class="bottom-navbar">
                <div class="container">
                    <ul class="nav page-navigation justify-content-start">
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == "dashboard" ? "active" : "" }}"
                                href="{{ route("dashboard") }}">
                                <i class="link-icon" data-feather="box"></i>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == "dosen.index" ? "active" : "" }}"
                                href="{{ route("dosen.index") }}">
                                <i class="link-icon" data-feather="database"></i>
                                <span class="menu-title">Data Dosen</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == "kriteria.index" ? "active" : "" }}"
                                href="{{ route("kriteria.index") }}">
                                <i class="link-icon" data-feather="database"></i>
                                <span class="menu-title">Data Kriteria</span>
                            </a>
                        </li>
                        <li class="nav-item mega-menu">
                            <a href="#" class="nav-link">
                                <i class="link-icon" data-feather="feather"></i>
                                <span class="menu-title">Perhitungan</span>
                                <i class="link-arrow"></i>
                            </a>
                            <div class="submenu">
                                <div class="col-group-wrapper row">
                                    <div class="col-group col-md-9">
                                        <div class="row">
                                            <div class="col-12">
                                                <p class="category-heading">Hasil Perhitungan AHP</p>
                                                <div class="submenu-item">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul>
                                                                <li class="nav-item"><a class="nav-link"
                                                                        href="{{ route("perhitungan.show.pendidikan-dan-pembelajaran") }}">Pendidikan
                                                                        Dan Pembelajaran</a></li>
                                                                <li class="nav-item"><a class="nav-link"
                                                                        href="{{ route("ahp.penelitian.index") }}">Penelitian
                                                                        (AHP)</a>
                                                                </li>
                                                                {{-- <li class="nav-item"><a class="nav-link"
                                                                        href="{{ route("ahp.tridarma.index") }}">AHP
                                                                        Tridarma</a>
                                                                </li> --}}
                                                                <li class="nav-item"><a class="nav-link"
                                                                        href="{{ route("pengabdian.index") }}">
                                                                        Pengabdian Kepada Masyarakat</a></li>
                                                                <li class="nav-item"><a class="nav-link"
                                                                        href="{{ route("perhitungan.penilaian.k004.page") }}">
                                                                        Kegiatan Penunjang Tridharma</a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul>
                                                                <li class="nav-item"><a class="nav-link"
                                                                        href="{{ route("ahp.tridarma.index") }}">Hasil
                                                                        Keseluruhan</a></li>
                                                                {{-- <li class="nav-item"><a class="nav-link"
                                                                        href="#"
                                                                        onclick="alert('Belum tersedia')">Ranking
                                                                        Final</a></li>
                                                                <li class="nav-item"><a class="nav-link"
                                                                        href="#"
                                                                        onclick="alert('Belum tersedia')">Laporan
                                                                        Lengkap</a></li> --}}
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), "ahp.") ? "active" : "" }}"
                                href="{{ route("ahp.dashboard") }}">
                                <i class="link-icon" data-feather="bar-chart-2"></i>
                                <span class="menu-title">AHP Dashboard</span>
                            </a>
                        </li> --}}
                    </ul>
                </div>
            </nav>
        </div>
        <!-- partial -->

        <div class="page-wrapper">

            <div class="page-content">

                @yield("konten")

            </div>

            <!-- partial:../../partials/_footer.html -->
            <footer class="footer border-top">
                <div
                    class="container d-flex flex-column flex-md-row align-items-center justify-content-between py-3 small">
                    <p class="text-muted mb-1 mb-md-0">Copyright © 2022 <a href="https://www.nobleui.com"
                            target="_blank">NobleUI</a>.</p>
                    <p class="text-muted">Handcrafted With <i class="mb-1 text-primary ms-1 icon-sm"
                            data-feather="heart"></i></p>
                </div>
            </footer>
            <!-- partial -->

        </div>
    </div>

    <!-- core:js -->
    <script src="{{ asset("vendors/core/core.js") }}"></script>
    <!-- endinject -->

    <!-- Plugin js for this page -->
    <script src="{{ asset("vendors/datatables.net/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendors/datatables.net-bs5/dataTables.bootstrap5.js") }}"></script>
    <!-- End plugin js for this page -->

    <!-- inject:js -->
    <script src="{{ asset("vendors/feather-icons/feather.min.js") }}"></script>
    <script src="{{ asset("js/template.js") }}"></script>
    <!-- endinject -->

    <!-- Custom js for this page -->
    <script src="{{ asset("js/data-table.js") }}"></script>
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>
    <!-- End custom js for this page -->

    @yield("js")
</body>

</html>
