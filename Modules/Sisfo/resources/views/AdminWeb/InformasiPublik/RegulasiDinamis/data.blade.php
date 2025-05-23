@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $regulasiDinamisUrlUrl = WebMenuModel::getDynamicMenuUrl('regulasi-dinamis');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $RegulasiDinamis->firstItem() }} to {{ $RegulasiDinamis->lastItem() }} of {{ $RegulasiDinamis->total() }} results
     </div>
 </div>
 
 <div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">Nomor</th>
             <th width="65%">Nama Regulasi Dinamis</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($RegulasiDinamis as $key => $item)
         <tr>
             <td>{{ ($RegulasiDinamis->currentPage() - 1) * $RegulasiDinamis->perPage() + $key + 1 }}</td>
             <td>{{ $item->rd_judul_reg_dinamis }}</td>
             <td class="text-center">
    <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $regulasiDinamisUrlUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($regulasiDinamisUrlUrl . '/editData/' . $item->regulasi_dinamis_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info mx-1"
                    onclick="modalAction('{{ url($regulasiDinamisUrlUrl . '/detailData/' . $item->regulasi_dinamis_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $regulasiDinamisUrlUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($regulasiDinamisUrlUrl . '/deleteData/' . $item->regulasi_dinamis_id) }}')">
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
     {{ $RegulasiDinamis->appends(['search' => $search])->links() }}
 </div>