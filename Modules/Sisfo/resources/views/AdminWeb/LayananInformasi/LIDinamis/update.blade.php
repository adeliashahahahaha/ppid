@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $liDinamisUrl = WebMenuModel::getDynamicMenuUrl('layanan-informasi-Dinamis');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Ubah Layanan Informasi Dinamis</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateLIDinamis" action="{{ url($liDinamisUrl . '/updateData/' . $liDinamis->li_dinamis_id) }}"
        method="POST">
        @csrf

        <div class="form-group">
            <label for="li_dinamis_kode">Kode Layanan Informasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="li_dinamis_kode" name="m_li_dinamis[li_dinamis_kode]" maxlength="50"
                value="{{ $liDinamis->li_dinamis_kode }}">
            <div class="invalid-feedback" id="li_dinamis_kode_error"></div>
        </div>

        <div class="form-group">
            <label for="li_dinamis_nama">Nama Layanan Informasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="li_dinamis_nama" name="m_li_dinamis[li_dinamis_nama]" maxlength="255"
                value="{{ $liDinamis->li_dinamis_nama }}">
            <div class="invalid-feedback" id="li_dinamis_nama_error"></div>
        </div>
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
            
            const form = $('#formUpdateLIDinamis');
            const formData = new FormData(form[0]);
            const button = $(this);
            
            // Tampilkan loading state pada tombol submit
            button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#myModal').modal('hide');
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
                                // Untuk m_li_dinamis fields
                                if (key.startsWith('m_li_dinamis.')) {
                                    const fieldName = key.replace('m_li_dinamis.', '');
                                    $(`#${fieldName}`).addClass('is-invalid');
                                    $(`#${fieldName}_error`).html(value[0]);
                                } else {
                                    // Untuk field biasa
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