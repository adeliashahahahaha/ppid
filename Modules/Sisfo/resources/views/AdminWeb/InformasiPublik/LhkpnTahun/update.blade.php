@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $kategoriTahunLHKPNUrl = WebMenuModel::getDynamicMenuUrl('kategori-tahun-lhkpn');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Edit Data LHKPN</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
     <form id="form-update-lhkpn" action="{{ url($kategoriTahunLHKPNUrl . '/updateData/' . $lhkpn->lhkpn_id) }}" method="POST"
         enctype="multipart/form-data">
         @csrf
         <input type="hidden" name="lhkpn_id" value="{{ $lhkpn->lhkpn_id }}">
         
         <div class="form-group">
             <label for="lhkpn_tahun">Tahun LHKPN <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="lhkpn_tahun" name="m_lhkpn[lhkpn_tahun]" 
                 maxlength="4" placeholder="Masukkan tahun LHKPN" value="{{ $lhkpn->lhkpn_tahun }}">
             <div class="invalid-feedback" id="lhkpn_tahun_error"></div>
             <small class="form-text text-muted">Contoh: 2023</small>
         </div>
 
         <div class="form-group">
             <label for="lhkpn_judul_informasi">Judul Informasi <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="lhkpn_judul_informasi" name="m_lhkpn[lhkpn_judul_informasi]" 
                 maxlength="255" placeholder="Masukkan judul informasi" value="{{ $lhkpn->lhkpn_judul_informasi }}">
             <div class="invalid-feedback" id="lhkpn_judul_informasi_error"></div>
         </div>
 
         <div class="form-group">
             <label for="lhkpn_deskripsi_informasi">Deskripsi Informasi <span class="text-danger">*</span></label>
             <textarea class="form-control" id="lhkpn_deskripsi_informasi" name="m_lhkpn[lhkpn_deskripsi_informasi]" 
                 rows="4" placeholder="Masukkan deskripsi informasi">{!! $lhkpn->lhkpn_deskripsi_informasi !!}</textarea>
             <div class="invalid-feedback" id="lhkpn_deskripsi_informasi_error"></div>
         </div>
         
         {{-- @if(isset($lhkpn->status))
         <div class="form-group">
             <label for="status">Status <span class="text-danger">*</span></label>
             <select class="form-control" id="status" name="m_lhkpn[status]">
                 <option value="">-- Pilih Status --</option>
                 <option value="aktif" {{ $lhkpn->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                 <option value="tidak aktif" {{ $lhkpn->status == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
             </select>
             <div class="invalid-feedback" id="status_error"></div>
         </div>
         @endif --}}
     </form>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-primary" id="btnSubmitForm">
         <i class="fas fa-save mr-1"></i> Simpan Perubahan
     </button>
 </div>
 
 <script>
    $(document).ready(function () {
    // Inisialisasi Summernote pada textarea deskripsi informasi
    $('#lhkpn_deskripsi_informasi').summernote({
        placeholder: 'Masukkan deskripsi informasi...',
        tabsize: 2,
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic', 'clear', 'fontsize', 'fontname']], 
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph', 'height', 'align']], 
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onChange: function(contents) {
                // Reset invalid state saat konten berubah
                $(this).next('.note-editor').removeClass('is-invalid');
                $('#lhkpn_deskripsi_informasi_error').html('');
            }
        }
    });

    // Tambahkan CSS untuk validasi error pada summernote
    $('<style>.note-editor.is-invalid {border: 1px solid #dc3545 !important;}</style>').appendTo('head');

    // Hapus error ketika input berubah
    $(document).on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        const errorId = `#${$(this).attr('id')}_error`;
        $(errorId).html('');
    });

    // Handle submit form
    $('#btnSubmitForm').on('click', function() {
        // Reset semua error
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        const form = $('#form-update-lhkpn');
        const formData = new FormData(form[0]);
        const button = $(this);

        // Validasi Tahun LHKPN (4 digit angka)
        const lhkpnTahun = $('#lhkpn_tahun').val();
        let isValid = true;
        
        // Validasi Tahun LHKPN
        if (lhkpnTahun === '') {
            isValid = false;
            $('#lhkpn_tahun').addClass('is-invalid');
            $('#lhkpn_tahun_error').html('Tahun LHKPN wajib diisi.');
        } else if (!/^\d{4}$/.test(lhkpnTahun)) {
            isValid = false;
            $('#lhkpn_tahun').addClass('is-invalid');
            $('#lhkpn_tahun_error').html('Tahun LHKPN harus berupa angka 4 digit.');
        }

        // Validasi Judul Informasi
        const lhkpnJudulInformasi = $('#lhkpn_judul_informasi').val();
        if (lhkpnJudulInformasi === '') {
            isValid = false;
            $('#lhkpn_judul_informasi').addClass('is-invalid');
            $('#lhkpn_judul_informasi_error').html('Judul Informasi wajib diisi.');
        }

        // Validasi Deskripsi Informasi (Summernote)
        const lhkpnDeskripsiInformasi = $('#lhkpn_deskripsi_informasi').summernote('code');
        if (lhkpnDeskripsiInformasi === '' || lhkpnDeskripsiInformasi === '<p><br></p>') {
            isValid = false;
            $('#lhkpn_deskripsi_informasi').next('.note-editor').addClass('is-invalid');
            $('#lhkpn_deskripsi_informasi_error').html('Deskripsi Informasi wajib diisi.');
        }

        // Validasi Status
        const status = $('#status').val();
        if (status === '') {
            isValid = false;
            $('#status').addClass('is-invalid');
            $('#status_error').html('Status wajib dipilih.');
        }

        // Jika form tidak valid, hentikan pengiriman
        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali input Anda'
            });
            return;
        }

        // Tambahkan konten Summernote ke formData
        formData.set('m_lhkpn[lhkpn_deskripsi_informasi]', lhkpnDeskripsiInformasi);
        
        // Tampilkan loading state pada tombol submit
        button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#myModal').modal('hide');
                    
                    // Reload tabel data
                    reloadTable();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                } else {
                    if (response.errors) {
                        // Tampilkan pesan error pada masing-masing field
                        $.each(response.errors, function(key, value) {
                            if (key.startsWith('m_lhkpn.')) {
                                const fieldName = key.replace('m_lhkpn.', '');
                                
                                // Penanganan khusus untuk deskripsi informasi (Summernote)
                                if (fieldName === 'lhkpn_deskripsi_informasi') {
                                    $('#lhkpn_deskripsi_informasi').next('.note-editor').addClass('is-invalid');
                                    $('#lhkpn_deskripsi_informasi_error').html(value[0]);
                                } else {
                                    $(`#${fieldName}`).addClass('is-invalid');
                                    $(`#${fieldName}_error`).html(value[0]);
                                }
                            } else {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).html(value[0]);
                            }
                        });
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Mohon periksa kembali input Anda'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan saat menyimpan data'
                        });
                    }
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                });
            },
            complete: function() {
                // Kembalikan tombol submit ke keadaan semula
                button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
            }
        });
    });
});

 </script>