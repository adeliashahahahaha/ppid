@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $detailAksesCepatUrl = WebMenuModel::getDynamicMenuUrl('detail-akses-cepat');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{$aksesCepat->firstItem() }} to {{$aksesCepat->lastItem() }} of {{$aksesCepat->total() }} results
    </div>
</div>
<div class="table-responsive">
<table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead class="text-center">
        <tr>
            <th width="5%">Nomor</th>
            <th width="20%">Judul Informasi Akses Cepat</th>
            <th width="15%">Icon Akses Cepat</th>
            <th width="15%">Icon Animasi Akses Cepat</th>
            <th width="20%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($aksesCepat as $key => $item)
        <tr>
            <td table-data-label>{{ ($aksesCepat->currentPage() - 1) *$aksesCepat->perPage() + $key + 1 }}</td>
           
            <td>{{ $item->ac_judul }}</td>
            <td>
                @if($item->ac_static_icon)
                    <img src="{{ asset('storage/akses_cepat_static_icons/' . basename($item->ac_static_icon)) }}"
                         alt="Static Icon" class="img-thumbnail" style="max-height: 50px;">
                @else
                 -
                @endif
            </td>
            <td>
                @if($item->ac_animation_icon)
                    <img src="{{ asset('storage/akses_cepat_animation_icons/' . basename($item->ac_animation_icon)) }}" 
                         alt="Animation Icon" class="img-thumbnail" style="max-height: 50px;">
                @else
                -
                @endif
            </td>
            <td class="text-center">
    <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailAksesCepatUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($detailAksesCepatUrl . '/editData/' . $item->akses_cepat_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info mx-1"
                    onclick="modalAction('{{ url($detailAksesCepatUrl . '/detailData/' . $item->akses_cepat_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailAksesCepatUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($detailAksesCepatUrl . '/deleteData/' . $item->akses_cepat_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
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
            </div>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">
    {{$aksesCepat->appends(['search' => $search])->links() }}
</div>

@push('css')
<style>
    .img-thumbnail {
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }
</style>
@endpush