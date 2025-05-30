@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $kategoriFooterUrl = WebMenuModel::getDynamicMenuUrl('kategori-footer');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Ubah Kategori Footer</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateKategoriFooter" action="{{ url($kategoriFooterUrl . '/updateData/' . $kategoriFooter->kategori_footer_id) }}"
        method="POST">
        @csrf
    
        <div class="form-group">
            <label for="kt_footer_kode">Kode Kategori Footer <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="kt_footer_kode" name="m_kategori_footer[kt_footer_kode]" maxlength="20"
                value="{{ $kategoriFooter->kt_footer_kode }}">
            <div class="invalid-feedback" id="kt_footer_kode_error"></div>
        </div>
    
        <div class="form-group">
            <label for="kt_footer_nama">Nama Kategori Footer <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="kt_footer_nama" name="m_kategori_footer[kt_footer_nama]" maxlength="100"
                value="{{ $kategoriFooter->kt_footer_nama }}">
            <div class="invalid-feedback" id="kt_footer_nama_error"></div>
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
      $(document).on('input change', 'input, select, textarea', function () {
        $(this).removeClass('is-invalid');
        const errorId = `#${$(this).attr('id')}_error`;
        $(errorId).html('');
      });
  
      // Fungsi validasi client-side
      function validateForm() {
        let isValid = true;
  
        const kode = $('#kt_footer_kode').val().trim();
        const nama = $('#kt_footer_nama').val().trim();
  
        if (kode === '') {
          $('#kt_footer_kode').addClass('is-invalid');
          $('#kt_footer_kode_error').html('Kode Kategori Footer wajib diisi.');
          isValid = false;
        } else if (kode.length > 20) {
          $('#kt_footer_kode').addClass('is-invalid');
          $('#kt_footer_kode_error').html('Maksimal 20 karakter.');
          isValid = false;
        }
  
        if (nama === '') {
          $('#kt_footer_nama').addClass('is-invalid');
          $('#kt_footer_nama_error').html('Nama Kategori Footer wajib diisi.');
          isValid = false;
        } else if (nama.length > 100) {
          $('#kt_footer_nama').addClass('is-invalid');
          $('#kt_footer_nama_error').html('Maksimal 100 karakter.');
          isValid = false;
        }
  
        return isValid;
      }
  
      $('#btnSubmitForm').on('click', function () {
        // Bersihkan error sebelumnya
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
  
        // Jalankan validasi client-side
        if (!validateForm()) {
          Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            text: 'Mohon periksa kembali input Anda.'
          });
          return;
        }
  
        const form = $('#formUpdateKategoriFooter');
        const formData = new FormData(form[0]);
        const button = $(this);
  
        button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
  
        $.ajax({
          url: form.attr('action'),
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
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
                $.each(response.errors, function (key, value) {
                  $(`#${key}`).addClass('is-invalid');
                  $(`#${key}_error`).html(value[0]);
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
          error: function (xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
            });
          },
          complete: function () {
            button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
          }
        });
      });
    });
  </script>
  