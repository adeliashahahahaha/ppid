@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $timelineUrl = WebMenuModel::getDynamicMenuUrl('timeline');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $timeline->firstItem() }} to {{ $timeline->lastItem() }} of {{ $timeline->total() }} results
    </div>
</div>

<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="30%">Kategori Timeline</th>
            <th width="35%">Judul Timeline</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($timeline as $key => $item)
        <tr>
            <td>{{ ($timeline->currentPage() - 1) * $timeline->perPage() + $key + 1 }}</td>
            <td>{{ $item->TimelineKategoriForm->kf_nama ?? '-' }}</td>
            <td>{{ $item->judul_timeline }}</td>
           <td class="text-center">
            <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $timelineUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($timelineUrl . '/editData/' . $item->timeline_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                    <button class="btn btn-sm btn-info mx-1"
                        onclick="modalAction('{{ url($timelineUrl . '/detailData/' . $item->timeline_id) }}')">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $timelineUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($timelineUrl . '/deleteData/' . $item->timeline_id) }}')">
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
    {{ $timeline->appends(['search' => $search])->links() }}
</div>