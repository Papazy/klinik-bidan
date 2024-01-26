<title>Dashboard</title>
@extends('layouts.main')
@section('content')
    <!------------------------------------- Isi TOTAL HARIAN ----------------------------------->
    <div class="d-flex flex-col flex-wrap gap-5">

        <div class="col col-4 m-2" >
            <!-- Pending Requests Card Example -->
            <div class="row-xl-3 row-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="col no-gutters align-items-center">
                            <div class="row mr-2 justify-content-between">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Daftar Harian</div>
                                <div class="h5 mb-0font-weight-bold text-gray-800">
                                    {{ $countpasientoday }}
                                </div>
                            </div>
                            <div class="row-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!------------------------------------- TOTAL Seluruh Pasien ----------------------------------->
        <div class="col col-4 m-2" >
            <!-- Pending Requests Card Example -->
            <div class="row-xl-3 row-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="col no-gutters align-items-center">
                            <div class="row mr-2 justify-content-between">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Seluruh Pasien</div>
                                <div class="h5 mb-0font-weight-bold text-gray-800">
                                    {{ count($pasien) }}
                                </div>
                            </div>
                            <div class="row-auto">
                                <i class="fas fa-book fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!------------------------------------- TOTAL Seluruh Pegawai ----------------------------------->
        <div class="col col-4 m-2" >
            <!-- Pending Requests Card Example -->
            <div class="row-xl-3 row-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="col no-gutters align-items-center">
                            <div class="row mr-2 justify-content-between">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Seluruh Pegawai</div>
                                <div class="h5 mb-0font-weight-bold text-gray-800">
                                    {{ count($pegawai) }}
                                </div>
                            </div>
                            <div class="row-auto">
                                <i class="fas fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!------------------------------------- TOTAL Laporan Harian ----------------------------------->
        <div class="col col-4 m-2" >
            <!-- Pending Requests Card Example -->
            <div class="row-xl-3 row-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="col no-gutters align-items-center">
                            <div class="row mr-2 justify-content-between">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Laporan</div>
                                <div class="h5 mb-0font-weight-bold text-gray-800">
                                    {{ $laporan }}
                                </div>
                            </div>
                            <div class="row-auto">
                                <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Footer -->
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>Copyright &copy; KLINIK {{ env('APP_NAME') }} 2024</span>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

    <!-- End of Page Wrapper -->
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
@endsection
