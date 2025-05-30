@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $detailLHKPNUrl = WebMenuModel::getDynamicMenuUrl('detail-lhkpn');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $detailLhkpn->firstItem() ?? 0 }} to {{ $detailLhkpn->lastItem() ?? 0 }} of {{ $detailLhkpn->total() ?? 0 }} results
     </div>
 </div>
 
 <div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">No</th>
             <th width="30%">Nama Karyawan</th>
             <th width="10%">Tahun</th>
             <th width="30%">Judul Informasi</th>
             <th width="25%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($detailLhkpn as $key => $item)
         <tr>
             <td>{{ ($detailLhkpn->currentPage() - 1) * $detailLhkpn->perPage() + $key + 1 }}</td>
             <td>{{ $item->dl_nama_karyawan }}</td>
             <td>{{ $item->lhkpn->lhkpn_tahun }}</td>
             <td>{{ $item->lhkpn->lhkpn_judul_informasi }}</td>
             <td class="text-center">
    <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailLHKPNUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($detailLHKPNUrl . '/editData/' . $item->detail_lhkpn_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info mx-1"
                    onclick="modalAction('{{ url($detailLHKPNUrl . '/detailData/' . $item->detail_lhkpn_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailLHKPNUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($detailLHKPNUrl . '/deleteData/' . $item->detail_lhkpn_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
                </div>
            </td>
         </tr>
         @empty
         <tr>
             <td colspan="5" class="text-center">
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
{{ $detailLhkpn->appends(['search' => $search])->links() }}
 </div>