@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $detailMediaUrlUrl = WebMenuModel::getDynamicMenuUrl('detail-media');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $detailMediaDinamis->firstItem() }} to {{ $detailMediaDinamis->lastItem() }} of {{$detailMediaDinamis->total() }} results
    </div>
</div>
<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="15%">Kategori</th>
            <th width="20%">Judul Media</th>
            <th width="15%">Tipe Media</th>
            <th width="20%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detailMediaDinamis as $key => $item)
        <tr>
            <td>{{ ($detailMediaDinamis->currentPage() - 1) * $detailMediaDinamis->perPage() + $key + 1 }}</td>
            <td>{{ $item->mediaDinamis->md_kategori_media ?? '-' }}</td>
            <td>{{ $item->dm_judul_media }}</td>
            <td>{{ ucfirst($item->dm_type_media) }}</td>
            <td class="text-center">
    <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailMediaUrlUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($detailMediaUrlUrl . '/editData/' . $item->detail_media_dinamis_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info mx-1"
                    onclick="modalAction('{{ url($detailMediaUrlUrl . '/detailData/' . $item->detail_media_dinamis_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailMediaUrlUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($detailMediaUrlUrl . '/deleteData/' . $item->detail_media_dinamis_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
                    </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">
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
    {{ $detailMediaDinamis->appends(['search' => $search])->links() }}
</div>