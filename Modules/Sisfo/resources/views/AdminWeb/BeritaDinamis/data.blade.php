@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $kategoriBeritaUrl = WebMenuModel::getDynamicMenuUrl('kategori-berita');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $kategoriBerita->firstItem() }} to {{ $kategoriBerita->lastItem() }} of {{ $kategoriBerita->total() }}
        results
    </div>
</div>

<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="10%">Nomor</th>
            <th width="60%">Nama Submenu Berita</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kategoriBerita as $key => $item)
            <tr>
                <td>{{ ($kategoriBerita->currentPage() - 1) * $kategoriBerita->perPage() + $key + 1 }}</td>
                <td>{{ $item->bd_nama_submenu }}</td>
                <td class="text-center">
    <div class="btn-group" role="group">
                    @if(
                        Auth::user()->level->hak_akses_kode === 'SAR' ||
                        SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriBeritaUrl, 'update')
                    )
                        <button class="btn btn-sm btn-warning mx-1"
                            onclick="modalAction('{{ url($kategoriBeritaUrl . '/editData/' . $item->berita_dinamis_id) }}')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    @endif
                    <button class="btn btn-sm btn-info mx-1"
                        onclick="modalAction('{{ url($kategoriBeritaUrl . '/detailData/' . $item->berita_dinamis_id) }}')">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                    @if(
                        Auth::user()->level->hak_akses_kode === 'SAR' ||
                        SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriBeritaUrl, 'delete')
                    )
                        <button class="btn btn-sm btn-danger mx-1"
                            onclick="modalAction('{{ url($kategoriBeritaUrl . '/deleteData/' . $item->berita_dinamis_id) }}')">
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
    {{ $kategoriBerita->appends(['search' => $search])->links() }}
</div>