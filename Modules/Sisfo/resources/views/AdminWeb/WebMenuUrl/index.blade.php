<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\WebMenuUrl\index.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
  $webMenuUrlPath = WebMenuModel::getDynamicMenuUrl('management-menu-url');
@endphp
@extends('sisfo::layouts.template')

@section('content')
  <div class="card card-outline card-primary">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h3 class="card-title">{{ $page->title }}</h3>
        </div>
        <div class="col-md-6 text-right">
          <div class="card-tools">
            @if(
              Auth::user()->level->hak_akses_kode === 'SAR' ||
              SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuUrlPath, 'create')
            )
              <button onclick="modalAction('{{ url($webMenuUrlPath . '/addData') }}')" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Tambah URL Menu
              </button>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <form id="searchForm" class="d-flex">
            <input type="text" name="search" class="form-control" placeholder="Cari nama URL, aplikasi..."
              value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary ml-2">
              <i class="fas fa-search"></i>
            </button>
          </form>
        </div>
      </div>

      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <div class="table-responsive" id="table-container">
        @include('sisfo::AdminWeb.WebMenuUrl.data')
      </div>
    </div>
  </div>

  <!-- Modal for CRUD operations -->
  <div id="myModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <!-- Modal content will be loaded here -->
      </div>
    </div>
  </div>
@endsection

@push('css')
  <style>
    .pagination {
      justify-content: flex-start;
    }
  </style>
@endpush

@push('js')
  <script>
    $(document).ready(function () {
      // URL dinamis untuk Management URL Menu
      var webMenuUrlPath = '{{ $webMenuUrlPath }}';

      // Handle search form submission
      $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        var search = $(this).find('input[name="search"]').val();
        loadWebMenuUrlData(1, search);
      });

      // Handle pagination links with delegation
      $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var search = $('#searchForm input[name="search"]').val();
        loadWebMenuUrlData(page, search);
      });

      // Fungsi untuk memuat data URL Menu
      function loadWebMenuUrlData(page, search) {
        $.ajax({
          url: '{{ url('') }}/' + webMenuUrlPath + '/getData',
          type: 'GET',
          data: {
            page: page,
            search: search
          },
          success: function (response) {
            $('#table-container').html(response);
          },
          error: function (xhr) {
            alert('Terjadi kesalahan saat memuat data');
          }
        });
      }

      // Fungsi untuk modal action
      window.modalAction = function(action) {
        $('#myModal .modal-content').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading...</p></div>');
        $('#myModal').modal('show');

        $.ajax({
          url: action,
          type: 'GET',
          success: function (response) {
            $('#myModal .modal-content').html(response);
          },
          error: function (xhr) {
            console.error('Ajax Error:', xhr);
            $('#myModal .modal-content').html(`
              <div class="modal-header">
                <h5 class="modal-title">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="alert alert-danger">
                  Terjadi kesalahan: ${xhr.status} ${xhr.statusText}
                </div>
              </div>
            `);
          }
        });
      }

      // Fungsi untuk me-reload tabel
      window.reloadTable = function() {
        var currentPage = $('.pagination .active .page-link').text();
        currentPage = currentPage || 1;
        var search = $('#searchForm input[name="search"]').val();
        loadWebMenuUrlData(currentPage, search);
      }
    });
  </script>
@endpush