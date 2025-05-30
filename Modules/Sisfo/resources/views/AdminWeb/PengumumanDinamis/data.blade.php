@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $kategoriPengumumanUrl = WebMenuModel::getDynamicMenuUrl('kategori-pengumuman');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $kategoriPengumuman->firstItem() }} to {{ $kategoriPengumuman->lastItem() }} of {{ $kategoriPengumuman->total() }} results
    </div>
</div>

<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="65%">Nama Submenu Pengumuman</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kategoriPengumuman as $key => $item)
        <tr>
            <td>{{ ($kategoriPengumuman->currentPage() - 1) * $kategoriPengumuman->perPage() + $key + 1 }}</td>
            <td>{{ $item->pd_nama_submenu }}</td>
            <td class="text-center">
    <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriPengumumanUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($kategoriPengumumanUrl . '/editData/' . $item->pengumuman_dinamis_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info mx-1"
                    onclick="modalAction('{{ url($kategoriPengumumanUrl . '/detailData/' . $item->pengumuman_dinamis_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriPengumumanUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($kategoriPengumumanUrl . '/deleteData/' . $item->pengumuman_dinamis_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center">
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
    {{ $kategoriPengumuman->appends(['search' => $search])->links() }}
</div>