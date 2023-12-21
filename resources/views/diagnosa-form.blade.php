<title>Diagnosa : {{ $diagnosa->pasien->nama }}</title>
    @include('partials.navdashboard')

    @if ($errors->any())
        @foreach ($errors->all() as $item)
            <div class="alert alert-danger" role="alert">
                {{ $item }}
            </div>
        @endforeach

    @endif
    <div class="container">
        <h1>Diagnosa Pasien</h1>
        <br>
        <form action="{{ route('diagnosatools.update', $diagnosa->id) }}" method="POST">
            @method('PATCH')
            @csrf

            </--------------------------------------------------------kodepasien-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kode Pasien</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="Kodepasien"
                        value="{{ $diagnosa->pasien->kodepasien }}" readonly>
                </div>
            </div>

            <!--------------------------------------------------------Lama / Baru----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Pasien Lama/Baru</label>
                <div class="col-sm-5">
                    <select name="Lamabaru" value="{{ old('Lamabaru') }}"
                        class="form-control" required oninvalid="this.setCustomValidity('pilih...')"
                        oninput="setCustomValidity('')">

                        <option selected value="">pilih...</option>
                        <option value="Baru" {{ old('Lamabaru') != 'Baru' ?: 'selected' }}>Baru</option>
                        <option value="Lama" {{ old('Lamabaru') != 'Lama' ?: 'selected' }}>Lama</option>
                    </select>
                </div>
            </div>

            <!--------------------------------------------------------kodepasien regis (bagi yang baru)------------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kode Regis Pasien</label>
                <div class="col-sm-5">
                    <input type="number" class="form-control" name="kodepasien" placeholder="Masukkan jika pasien baru"
                        value="{{ old('Kodepasien-Regis') }}">
                </div>
            </div>

            </--------------------------------------------------------Nama-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="Nama" placeholder="Nama" required="required"
                        value="{{ $diagnosa->pasien->nama }}"
                        oninvalid="this.setCustomValidity('Nama tidak boleh kosong')" oninput="setCustomValidity('')"
                        readonly>
                </div>
            </div>

            </--------------------------------------------------------NIK-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">NIK (Kartu Keluarga)</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="NIK" value="{{ $diagnosa->pasien->nik }}"
                        readonly>
                </div>
            </div>

            </--------------------------------------------------------Lahir-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Lahir</label>
                <div class="col-sm-5">
                    <input type="date" class="form-control" name="Lahir"
                        value="{{ $diagnosa->pasien->lahir->format('Y-m-d') }}" readonly>
                </div>
            </div>

            <!--------------------------------------------------------Layanan----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jenis layanan</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="Layanan" required="required"
                        value="{{ $diagnosa->layanan }}"
                        oninvalid="this.setCustomValidity('layanan tidak boleh kosong')" oninput="setCustomValidity('')"
                        readonly>
                </div>
            </div>

            <!--------------------------------------------------------keluhan pasien----------------------------------------------------------------------------------- -->
            <div class="form-group row mt-2">
                <label class="col-sm-2 col-form-label">Keluhan</label>
                <div class="col-sm-5">
                    {{-- <input type="text" class="form-control" name="RekamMedis"
                    placeholder="Anda sakit apa, dan sudah berapa lama?"> --}}
                    <textarea readonly type="text" name="RekamMedis" class="form-control" cols="30" rows="5">{{ $diagnosa->keluhan }}</textarea>
                </div>
            </div>

            <!--------------------------------------------------------dokter pemriksa----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Dokter</label>
                <div class="col-sm-5">
                    <input type="text" value="{{ $diagnosa->dokter->nama ?? "-"}}" readonly class="form-control">
                </div>
            </div>

            </--------------------------------------------------------umur-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Umur</label>
                <div class="col-sm-5">
                    <h1> {{ $diagnosa->pasien->lahir->age }} Tahun </h1>
                </div>
            </div>

            <!--------------------------------------------------------DIAGNOSA----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Diagnosa</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="diagnosa" placeholder="Diagnosa.."
                        required value="{{ old('diagnosa') }}"
                        oninvalid="this.setCustomValidity('Diagnosa tidak boleh kosong')"
                        oninput="setCustomValidity('')">
                </div>
            </div>

            <!--------------------------------------------------------Obat----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Obat</label>
                <div class="col-sm-5">
                    <select name="obat" id="" class="form-control">
                        <option value="">Pilih obat..</option>
                        @foreach ($obat as $o)
                            <option value="{{ $o->id }}">{{ $o->nama . ' (Sisa stok: ' . $o->stok . ')' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!--------------------------------------------------------banyaknya Obat----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jumlah Obat</label>
                <div class="col-sm-5">
                    <input type="number" class="form-control" name="jumlahobat" placeholder="Jumlah obat"
                        value="{{ old('Nama') }}"
                        oninvalid="this.setCustomValidity('Jumlah Obat tidak boleh kosong')"
                        oninput="setCustomValidity('')">
                </div>
            </div>

            <!--------------------------------------------------------keterangan----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="keterangan" placeholder="Keterangan.."
                        value="{{ old('keterangan') }}"
                        oninvalid="this.setCustomValidity('Keterangan tidak boleh kosong')"
                        oninput="setCustomValidity('')">
                </div>
            </div>

            </--------------------------------------------------------jalan /
                inap-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-form-label col-sm-2 pt-0">Ruang</label>
                <div class="col-sm-5">
                    <select name="Ruang" value="{{ old('Ruang') }}"
                        class="form-control @error('Ruang') is-invalid @enderror">

                        <option selected value="">pilih...</option>
                        <option value="R.Jalan" {{ old('Ruang') != 'R.Jalan' ?: 'selected' }}>R. Jalan</option>
                        <option value="R.Inap" {{ old('Ruang') != 'R.Inap' ?: 'selected' }}>R. Inap</option>
                    </select>
                    @error('Ruang')
                        <div class="invalid-feedback">
                            "pilih jalan / inap
                        </div>
                    @enderror
                </div>
            </div>

            <h5>==== khusus penyakit tertentu ====</h5>

            </--------------------------------------------------------Golongan
                darah-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-form-label col-sm-2 pt-0">Golongan Darah</label>
                <div class="col-sm-5">
                    <select name="Darah" value="{{ old('Darah') }}" class="form-control">
                        <option selected value="-">pilih...</option>
                        <option value="A+" {{ old('Darah') != 'A+' ?: 'selected' }}>A+</option>
                        <option value="B+" {{ old('Darah') != 'B+' ?: 'selected' }}>B+</option>
                        <option value="AB+" {{ old('Darah') != 'AB+' ?: 'selected' }}>AB+</option>
                        <option value="O+" {{ old('Darah') != 'O+' ?: 'selected' }}>O+</option>
                        <option value="A-" {{ old('Darah') != 'A-' ?: 'selected' }}>A-</option>
                        <option value="B-" {{ old('Darah') != 'B-' ?: 'selected' }}>B-</option>
                        <option value="AB-" {{ old('Darah') != 'AB-' ?: 'selected' }}>AB-</option>
                        <option value="O-" {{ old('Darah') != 'O-' ?: 'selected' }}>O-</option>
                    </select>
                </div>
            </div>

            <!--------------------------------------------------------Tinggi Badan----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tinggi Badan</label>
                <div class="col-sm-5">
                    <input type="number" class="form-control" name="Tinggi" placeholder="Tinggi Badan (Cm)"
                        value="{{ old('Tinggi') }}">
                </div>
            </div>

            <!--------------------------------------------------------Berat Badan----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Berat Badan</label>
                <div class="col-sm-5">
                    <input type="number" class="form-control" name="Berat" placeholder="Berat Badan (Kg)"
                        value="{{ old('Berat') }}">
                </div>
            </div>

            <!--------------------------------------------------------lingkar Pinggang----------------------------------------------------------------------------------- -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Lingkar Badan</label>
                <div class="col-sm-5">
                    <input type="number" class="form-control" name="LingkarBadan" placeholder="Lingkar Badan (Cm)"
                        value="{{ old('LingkarBadan') }}">
                </div>
            </div>


            </--------------------------------------------------------Alamat-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="Alamat"
                        value="{{ $diagnosa->pasien->alamat }}" readonly>
                </div>
            </div>

            </--------------------------------------------------------No
                Handphone-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nomer Handphone</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="Telepon"
                        value="{{ $diagnosa->pasien->telepon }}" readonly>
                </div>
            </div>

            </--------------------------------------------------------Pendidikan-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Pendidikan</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="Pendidikan"
                        value="{{ $diagnosa->pasien->pendidikan }}" readonly>
                </div>
            </div>

            </--------------------------------------------------------Pekerjaan-----------------------------------------------------------------------------------* />
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Pekerjaan</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="Pekerjaan"
                        value="{{ $diagnosa->pasien->pekerjaan }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">DIAGNOSA</button>
                    <a href="/diagnosa" class="btn btn-warning">Kembali</a>
                </div>
            </div>
        </form>
    </div>

    <script>
        function setInputFilter(textbox, inputFilter, errMsg) {
            ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(
                function(event) {
                    textbox.addEventListener(event, function(e) {
                        if (inputFilter(this.value)) {
                            // Accepted value
                            if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
                                this.classList.remove("input-error");
                                this.setCustomValidity("");
                            }
                            this.oldValue = this.value;
                            this.oldSelectionStart = this.selectionStart;
                            this.oldSelectionEnd = this.selectionEnd;
                        } else if (this.hasOwnProperty("oldValue")) {
                            // Rejected value - restore the previous one
                            this.classList.add("input-error");
                            this.setCustomValidity(errMsg);
                            this.reportValidity();
                            this.value = this.oldValue;
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                        } else {
                            // Rejected value - nothing to restore
                            this.value = "";
                        }
                    });
                });
        }

        setInputFilter(document.getElementById("nonik"), function(value) {
            return /^-?\d*$/.test(value);
        }, "Isi dengan Angka");
        setInputFilter(document.getElementById("notelp"), function(value) {
            return /^-?\d*$/.test(value);
        }, "Isi dengan Angka");
    </script>
