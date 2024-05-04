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



  <!--------------------------------------------------------modal kartu check pasien----------------------------------------------------------------------------------->
  <div class="modal fade" id="myModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="antrianLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <!-- Tambahkan kelas modal-lg di sini -->
      <div class="modal-content">
        <div class="modal-header">
          <img src="{{ asset('img/logo.png') }}" style="float: left; width: 55px; height: 55px;" class="me-2" alt="Logo">
          <h5 class="modal-title">Data Antrian Pasien</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="h3">Nama: <span class="text-primary"></span></p>
          <p>Nomor antrian: <span class="text-primary"></span></p>
          <p>jadwal: <span class="text-primary"></span></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="download">Tutup</button>
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
{{--  --}}
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


  //<!--------------------------------------------------------Scan Antrian----------------------------------------------------------------------------------->
  function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    console.log(`Code matched = ${decodedText}`, decodedResult);

    // html5QrcodeScanner.clear(); // Atau html5QrcodeScanner.stop()

    var ajaxRequest = $.ajax({
      url: 'http://127.0.0.1:8000/checkqr', // Ubah URL sesuai dengan URL endpoint server Anda
      method: 'POST'
      , data: {
        "_token": "{{ csrf_token() }}",
        decodedText: decodedText
      }
      , success: function(response) {
        // Mendapatkan data dari respons
        var data = response.data;
        console.log(data);
        // Mengisi elemen-elemen HTML dalam modal dengan data yang diterima
        $('#myModal .modal-body .h3 span').text(data.user.nama);
        $('#myModal .modal-body p:nth-child(2) span').text(data.antrian.no_antrian);
        $('#myModal .modal-body p:nth-child(3) span').text(data.jadwal);
        // Tampilkan modal dengan data yang diterima dari server
        $('#myModal').modal('show');
      }
      , error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
    console.log(ajaxRequest);
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
        width: 400
        , height: 400
      }
    },
    /* verbose= */
    false);
  html5QrcodeScanner.render(onScanSuccess, onScanFailure);

</script>
@endpush
@endsection
