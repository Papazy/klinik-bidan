<title>Antrian-Pasien (Jam {{ \Carbon\Carbon::now()->format("H:i") }})</title>
@extends('layouts.main')
@section('content')
@if ($errors->any())
@foreach ($errors->all() as $item)
<div class="alert alert-danger" role="alert">
    {{ $item }}
</div>
@endforeach
@endif

@if (session()->has('success'))
<div class="alert alert-success" role="alert">
    {{ session('success') }}
</div>
@endif

<div class="container">
    <h1>Data Antrian Pasien Harian</h1>
    <br>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upc-scan" viewBox="0 0 16 16">
            <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
        </svg>
        Scan Antrian
    </button>



    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div id="reader" width="600px"></div>
        </div>
        <div class="col-4">
            <input type="text" id="results">
        </div>
    </div>



    <br>
    {{-- </--------------------------------------------------------
        Tabel-----------------------------------------------------------------------------------* /> --}}
    <br />
    <div class="table-responsive">
        <table class="table table-flush" id="products-list">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Tools</th>
                    <th>No Antrian</th>
                    <th>Jam Daftar</th>
                    <th>Nama</th>
                    <th>Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    {{-- <th>Jenis Layanan</th> --}}
                    <th>Keluhan</th>
                    {{-- <th>Dokter</th> --}}

                    <th>Alamat</th>
                    <th>NIK</th>
                    <th>Nomer Telepon</th>
                    <th>Agama</th>
                    <th>Pendidikan</th>
                    <th>Pekerjaan</th>
                </tr>
            </thead>
            <tbody>
                @php
                $count = 0;
                @endphp
                @foreach($datarekam as $row)
                <tr>
                    <td>{{ $count = $count + 1 }}</td>
                    <td class="d-flex gap-1">
                        <div>
                            <a href="{{ route('rekam.edit', $row->id) }}" class="btn btn-warning me-2" data-bs-toggle="tooltip" data-bs-original-title="Lihat Pasien">
                                <i class="fas fa-pen text-white"></i>
                            </a>
                        </div>
                        <div class="ml-1">

                            <form action="{{ route('rekam.destroy', $row->id) }}" method="POST">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-danger" onClick="return confirm('Yakin ingin hapus data?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    <td>{{ $row->no_antrian }}</td>
                    <td>{{ $row->updated_at->format('H:i:s -- d/m/Y'); }}</td>
                    <td>{{ $row->pasien->nama }}</td>
                    <td>{{ $row->pasien->lahir->format('d/M/Y'); }}</td>
                    <td>{{ $row->pasien->kelamin }}</td>
                    {{-- <td>{{ $row->layanan }}</td> --}}
                    <td>{{ $row->keluhan }}</td>
                    {{-- <td>{{ $row->dokter->nama ?? "Dokter Tidak ada"}}</td> --}}
                    <td>{{ $row->pasien->alamat }}</td>
                    <td>{{ $row->pasien->nik }}</td>
                    <td>
                        <a href="https://api.whatsapp.com/send?phone=<?php echo $row->pasien['telepon']; ?>" target=" _blank" title="Pesan WhatsApp" class="btn btn-success">
                            <b>{{ $row->pasien->telepon }}</b>
                        </a>

                    </td>
                    <td>{{ $row->pasien->agama }}</td>
                    <td>{{ $row->pasien->pendidikan }}</td>
                    <td>{{ $row->pasien->pekerjaan }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>



</div>
@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#products-list').DataTable({
            dom: 'lBfrtip'
            , lengthMenu: [
                [50, 100, 5, -1]
                , ['50', '100', '5', 'All']
            ]
            , buttons: [{
                    extend: 'excel'
                    , text: 'Excel'
                    , messageTop: 'Data Antrian Harian per Tanggal ' + '{{  \Carbon\Carbon::now()->format("d-M(m)-Y") }}'

                }
                , {
                    extend: 'copy'
                    , text: 'Copy Isi'
                    , messageTop: 'Data Antrian Harian per Tanggal ' + '{{  \Carbon\Carbon::now()->format("d-M(m)-Y") }}'

                }
            , ]
            , language: {
                "searchPlaceholder": "Cari nama pasien"
                , "zeroRecords": "Tidak ditemukan data yang sesuai"
                , "emptyTable": "Tidak terdapat data di tabel"
            }
        });
    });

    // <!--------------------------------------------------------auto refresh page----------------------------------------------------------------------------------->
    setTimeout(function() {
        window.location.reload();
    }, 16000);


    //<!--------------------------------------------------------Scan Antrian----------------------------------------------------------------------------------->
    function onScanSuccess(decodedText, decodedResult) {
        // handle the scanned code as you like, for example:
        console.log(`Code matched = ${decodedText}`, decodedResult);
        var resultsInput = document.getElementById("results");
        resultsInput.value = decodedText;
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning.
        // for example:
        console.warn(`Code scan error = ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", {
            fps: 10
            , qrbox: {
                width: 250
                , height: 250
            }
        },
        /* verbose= */
        false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

</script>
@endpush
@endsection
