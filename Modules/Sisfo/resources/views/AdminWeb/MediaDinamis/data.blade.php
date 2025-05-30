@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $kategoriMediaUrl = WebMenuModel::getDynamicMenuUrl('kategori-media');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $mediaDinamis->firstItem() }} to {{ $mediaDinamis->lastItem() }} of {{ $mediaDinamis->total() }} results
     </div>
 </div>
 
 <div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="10%">Nomor</th>
             <th width="60%">Kategori Media</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($mediaDinamis as $key => $item)
         <tr>
             <td>{{ ($mediaDinamis->currentPage() - 1) * $mediaDinamis->perPage() + $key + 1 }}</td>
             <td>{{ $item->md_kategori_media }}</td>
            <td class="text-center">
    <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriMediaUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($kategoriMediaUrl . '/editData/' . $item->media_dinamis_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info mx-1"
                    onclick="modalAction('{{ url($kategoriMediaUrl . '/detailData/' . $item->media_dinamis_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriMediaUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($kategoriMediaUrl . '/deleteData/' . $item->media_dinamis_id) }}')">
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
     {{ $mediaDinamis->appends(['search' => $search])->links() }}
 </div>