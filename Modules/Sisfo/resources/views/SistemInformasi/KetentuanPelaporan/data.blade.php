@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $ketentuanPelaporanUrl = WebMenuModel::getDynamicMenuUrl('ketentuan-pelaporan');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $ketentuanPelaporan->firstItem() }} to {{ $ketentuanPelaporan->lastItem() }} of {{ $ketentuanPelaporan->total() }} results
    </div>
</div>

<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="20%">Kategori Form</th>
            <th width="45%">Judul Ketentuan</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ketentuanPelaporan as $key => $item)
        <tr>
            <td>{{ ($ketentuanPelaporan->currentPage() - 1) * $ketentuanPelaporan->perPage() + $key + 1 }}</td>
            <td>{{ $item->PelaporanKategoriForm->kf_nama ?? '-' }}</td>
            <td>{{ $item->kp_judul }}</td>
           <td class="text-center">
            <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $ketentuanPelaporanUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($ketentuanPelaporanUrl . '/editData/' . $item->ketentuan_pelaporan_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                    <button class="btn btn-sm btn-info mx-1"
                        onclick="modalAction('{{ url($ketentuanPelaporanUrl . '/detailData/' . $item->ketentuan_pelaporan_id) }}')">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $ketentuanPelaporanUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($ketentuanPelaporanUrl . '/deleteData/' . $item->ketentuan_pelaporan_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
                 </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">
                @if(!empty($search))
                    Tidak ada data yang cocok dengan pencarian "{{ $search }}"
                @else
                    Tidak ada data
                @endif
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">
    {{ $ketentuanPelaporan->appends(['search' => $search])->links() }}
</div>