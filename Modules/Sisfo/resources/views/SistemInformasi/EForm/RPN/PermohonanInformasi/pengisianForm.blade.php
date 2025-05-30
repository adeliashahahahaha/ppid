<!-- pengisian form halaman responden -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $permohonanInformasiRespondenUrl = WebMenuModel::getDynamicMenuUrl('permohonan-informasi');
@endphp
@extends('sisfo::layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href= "{{ url($permohonanInformasiRespondenUrl) }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Permohonan Informasi </strong></h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

            <form action="{{ url($permohonanInformasiRespondenUrl . '/createData') }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                <div class="form-group">
                    <label for="pi_kategori_pemohon">Permohonan Informasi Dilakukan Atas <span class="text-danger">*</span></label>
                    <select class="form-control @error('t_permohonan_informasi.pi_kategori_pemohon') is-invalid @enderror" 
                        id="pi_kategori_pemohon" name="t_permohonan_informasi[pi_kategori_pemohon]" required>
                        <option value="">-- Silakan Pilih Kategori Pemohon --</option>
                        <option value="Diri Sendiri" {{ old('t_permohonan_informasi.pi_kategori_pemohon') == 'Diri Sendiri' ? 'selected' : '' }}>Diri Sendiri</option>
                        <option value="Orang Lain" {{ old('t_permohonan_informasi.pi_kategori_pemohon') == 'Orang Lain' ? 'selected' : '' }}>Orang Lain</option>
                        <option value="Organisasi" {{ old('t_permohonan_informasi.pi_kategori_pemohon') == 'Organisasi' ? 'selected' : '' }}>Organisasi</option>
                    </select>
                    @error('t_permohonan_informasi.pi_kategori_pemohon')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div id="formDiriSendiri" style="display: none;">
                    <div class="alert alert-info">
                        Data Diri Anda seperti Nama lengkap, Alamat Email, No Hp, Foto Identitas(NIK), akan digunakan sebagai data pengajuan.
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nama Lengkap:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->nama_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Alamat:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->alamat_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->email_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nomor HP:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->no_hp_pengguna }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form untuk Orang Lain -->
                <div id="formOrangLain" style="display: none;">
                    <div class="alert alert-info">
                        Data diri Anda akan digunakan sebagai data pelapor seperti Nama lengkap, Alamat Email, No Hp, Foto Identitas(NIK), akan digunakan sebagai data pengajuan.
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nama Lengkap:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->nama_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Alamat:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->alamat_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->email_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nomor HP:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->no_hp_pengguna }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pi_nama_pengguna_informasi">Nama Pengguna Informasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pi_orang_lain.pi_nama_pengguna_informasi') is-invalid @enderror" 
                            id="pi_nama_pengguna_informasi" name="t_form_pi_orang_lain[pi_nama_pengguna_informasi]" 
                            value="{{ old('t_form_pi_orang_lain.pi_nama_pengguna_informasi') }}">
                        @error('t_form_pi_orang_lain.pi_nama_pengguna_informasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pi_alamat_pengguna_informasi">Alamat Pengguna Informasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pi_orang_lain.pi_alamat_pengguna_informasi') is-invalid @enderror" 
                            id="pi_alamat_pengguna_informasi" name="t_form_pi_orang_lain[pi_alamat_pengguna_informasi]" 
                            value="{{ old('t_form_pi_orang_lain.pi_alamat_pengguna_informasi') }}">
                        @error('t_form_pi_orang_lain.pi_alamat_pengguna_informasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pi_no_hp_pengguna_informasi">No HP Pengguna Informasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pi_orang_lain.pi_no_hp_pengguna_informasi') is-invalid @enderror" 
                            id="pi_no_hp_pengguna_informasi" name="t_form_pi_orang_lain[pi_no_hp_pengguna_informasi]" 
                            value="{{ old('t_form_pi_orang_lain.pi_no_hp_pengguna_informasi') }}">
                        @error('t_form_pi_orang_lain.pi_no_hp_pengguna_informasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pi_email_pengguna_informasi">Email Pengguna Informasi <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('t_form_pi_orang_lain.pi_email_pengguna_informasi') is-invalid @enderror" 
                            id="pi_email_pengguna_informasi" name="t_form_pi_orang_lain[pi_email_pengguna_informasi]" 
                            value="{{ old('t_form_pi_orang_lain.pi_email_pengguna_informasi') }}">
                        @error('t_form_pi_orang_lain.pi_email_pengguna_informasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pi_upload_nik_pengguna_informasi">Upload NIK Pengguna Informasi <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('pi_upload_nik_pengguna_informasi') is-invalid @enderror" 
                            id="pi_upload_nik_pengguna_informasi" name="pi_upload_nik_pengguna_informasi" accept="image/*">
                        @error('pi_upload_nik_pengguna_informasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form untuk Organisasi -->
                <div id="formOrganisasi" style="display: none;">
                    <div class="form-group">
                        <label for="pi_nama_organisasi">Nama Organisasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pi_organisasi.pi_nama_organisasi') is-invalid @enderror"
                            id="pi_nama_organisasi" name="t_form_pi_organisasi[pi_nama_organisasi]" 
                            value="{{ old('t_form_pi_organisasi.pi_nama_organisasi') }}">
                        @error('t_form_pi_organisasi.pi_nama_organisasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pi_no_telp_organisasi">No Telepon Organisasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pi_organisasi.pi_no_telp_organisasi') is-invalid @enderror"
                            id="pi_no_telp_organisasi" name="t_form_pi_organisasi[pi_no_telp_organisasi]" 
                            value="{{ old('t_form_pi_organisasi.pi_no_telp_organisasi') }}">
                        @error('t_form_pi_organisasi.pi_no_telp_organisasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pi_email_atau_medsos_organisasi">Email/Media Sosial Organisasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pi_organisasi.pi_email_atau_medsos_organisasi') is-invalid @enderror"
                            id="pi_email_atau_medsos_organisasi" name="t_form_pi_organisasi[pi_email_atau_medsos_organisasi]"
                            value="{{ old('t_form_pi_organisasi.pi_email_atau_medsos_organisasi') }}">
                        @error('t_form_pi_organisasi.pi_email_atau_medsos_organisasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pi_nama_narahubung">Nama Narahubung <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pi_organisasi.pi_nama_narahubung') is-invalid @enderror"
                            id="pi_nama_narahubung" name="t_form_pi_organisasi[pi_nama_narahubung]" 
                            value="{{ old('t_form_pi_organisasi.pi_nama_narahubung') }}">
                        @error('t_form_pi_organisasi.pi_nama_narahubung')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pi_no_telp_narahubung">No Telepon Narahubung <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pi_organisasi.pi_no_telp_narahubung') is-invalid @enderror"
                            id="pi_no_telp_narahubung" name="t_form_pi_organisasi[pi_no_telp_narahubung]"
                            value="{{ old('t_form_pi_organisasi.pi_no_telp_narahubung') }}">
                        @error('t_form_pi_organisasi.pi_no_telp_narahubung')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pi_identitas_narahubung">Upload Identitas Narahubung <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('pi_identitas_narahubung') is-invalid @enderror"
                            id="pi_identitas_narahubung" name="pi_identitas_narahubung" accept="image/*">
                        @error('pi_identitas_narahubung')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form umum untuk semua kategori -->
                <div class="form-group">
                    <label for="pi_informasi_yang_dibutuhkan">Informasi yang Dibutuhkan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('t_permohonan_informasi.pi_informasi_yang_dibutuhkan') is-invalid @enderror" 
                        id="pi_informasi_yang_dibutuhkan" name="t_permohonan_informasi[pi_informasi_yang_dibutuhkan]" 
                        required rows="4">{{ old('t_permohonan_informasi.pi_informasi_yang_dibutuhkan') }}</textarea>
                    @error('t_permohonan_informasi.pi_informasi_yang_dibutuhkan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="pi_alasan_permohonan_informasi">Alasan Permohonan Informasi <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('t_permohonan_informasi.pi_alasan_permohonan_informasi') is-invalid @enderror" 
                        id="pi_alasan_permohonan_informasi" name="t_permohonan_informasi[pi_alasan_permohonan_informasi]" 
                        required rows="4">{{ old('t_permohonan_informasi.pi_alasan_permohonan_informasi') }}</textarea>
                    @error('t_permohonan_informasi.pi_alasan_permohonan_informasi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Sumber Informasi <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input @error('t_permohonan_informasi.pi_sumber_informasi') is-invalid @enderror" 
                            type="radio" id="sumber_1" name="t_permohonan_informasi[pi_sumber_informasi]"
                            value="Pertanyaan Langsung Pemohon" 
                            {{ old('t_permohonan_informasi.pi_sumber_informasi') == 'Pertanyaan Langsung Pemohon' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="sumber_1">Pertanyaan Langsung Pemohon</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_permohonan_informasi.pi_sumber_informasi') is-invalid @enderror" 
                            type="radio" id="sumber_2" name="t_permohonan_informasi[pi_sumber_informasi]"
                            value="Website / Media Sosial Milik Polinema" 
                            {{ old('t_permohonan_informasi.pi_sumber_informasi') == 'Website / Media Sosial Milik Polinema' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_2">Website / Media Sosial Milik Polinema</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_permohonan_informasi.pi_sumber_informasi') is-invalid @enderror" 
                            type="radio" id="sumber_3" name="t_permohonan_informasi[pi_sumber_informasi]"
                            value="Website / Media Sosial Bukan Milik Polinema" 
                            {{ old('t_permohonan_informasi.pi_sumber_informasi') == 'Website / Media Sosial Bukan Milik Polinema' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Website / Media Sosial Bukan Milik Polinema</label>
                    </div>
                    @error('t_permohonan_informasi.pi_sumber_informasi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="pi_alamat_sumber_informasi">Alamat Sumber Informasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('t_permohonan_informasi.pi_alamat_sumber_informasi') is-invalid @enderror" 
                        id="pi_alamat_sumber_informasi" name="t_permohonan_informasi[pi_alamat_sumber_informasi]" 
                        value="{{ old('t_permohonan_informasi.pi_alamat_sumber_informasi') }}" required>
                    @error('t_permohonan_informasi.pi_alamat_sumber_informasi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info mt-3 mb-4">
                    <p class="mb-0"><strong>Catatan:</strong> Dengan mengajukan laporan ini, Anda menyatakan bahwa informasi yang diberikan adalah benar dan Anda bersedia memberikan keterangan lebih lanjut jika diperlukan.</p>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="persetujuan" required>
                        <label class="custom-control-label" for="persetujuan">Saya menyatakan bahwa informasi yang saya berikan adalah benar dan dapat dipertanggungjawabkan</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Ajukan Permohonan Informasi</button>
            </form>
        </div>
    </div>

    @push('js')
        <script>
           $(document).ready(function () {
                // Tampilkan form yang sesuai saat halaman di-load berdasarkan nilai yang tersimpan
                const savedValue = "{{ old('t_permohonan_informasi.pi_kategori_pemohon') }}";
                if (savedValue) {
                    showFormBasedOnSelection(savedValue);
                }
                
                $('#pi_kategori_pemohon').change(function () {
                    const selectedValue = $(this).val();
                    showFormBasedOnSelection(selectedValue);
                });
                
                function showFormBasedOnSelection(selectedValue) {
                    // Sembunyikan semua form tambahan
                    $('#formDiriSendiri, #formOrangLain, #formOrganisasi').hide();

                    // Reset required attributes
                    $('#formOrangLain input, #formOrganisasi input').prop('required', false);

                    // Tampilkan form sesuai pilihan
                    if (selectedValue === 'Orang Lain') {
                        $('#formOrangLain').show();
                        $('#formOrangLain input:not([type="file"])').prop('required', true);
                        $('#pi_upload_nik_pengguna_penginput, #pi_upload_nik_pengguna_informasi').prop('required', true);
                    } else if (selectedValue === 'Organisasi') {
                        $('#formOrganisasi').show();
                        $('#formOrganisasi input:not([type="file"])').prop('required', true);
                        $('#pi_identitas_narahubung').prop('required', true);
                    } else if (selectedValue === 'Diri Sendiri') {
                        $('#formDiriSendiri').show();
                    }
                }

                $('#persetujuan').change(function() {
                    if($(this).is(':checked')) {
                        $('#btnSubmit').prop('disabled', false);
                    } else {
                        $('#btnSubmit').prop('disabled', true);
                    }
                });
            });
        </script>
    @endpush
@endsection