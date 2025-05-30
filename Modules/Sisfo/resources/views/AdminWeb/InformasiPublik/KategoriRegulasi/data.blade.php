@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $regulasiDinamisUrl = WebMenuModel::getDynamicMenuUrl('kategori-regulasi');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $kategoriRegulasi->firstItem() }} to {{ $kategoriRegulasi->lastItem() }} of {{ $kategoriRegulasi->total() }} results
     </div>
 </div>
 
 <div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">Nomor</th>
             <th width="20%">Regulasi Dinamis</th>
             <th width="15%">Kode Kategori</th>
             <th width="35%">Nama Kategori Regulasi</th>
             <th width="25%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($kategoriRegulasi as $key => $item)
         <tr>
             <td>{{ ($kategoriRegulasi->currentPage() - 1) * $kategoriRegulasi->perPage() + $key + 1 }}</td>
             <td>{{ $item->RegulasiDinamis->rd_judul_reg_dinamis ?? 'Tidak ada' }}</td>
             <td>{{ $item->kr_kategori_reg_kode }}</td>
             <td>{{ $item->kr_nama_kategori }}</td>
             <td class="text-center">
    <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $regulasiDinamisUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($regulasiDinamisUrl . '/editData/' . $item->kategori_reg_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info mx-1"
                    onclick="modalAction('{{ url($regulasiDinamisUrl . '/detailData/' . $item->kategori_reg_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $regulasiDinamisUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($regulasiDinamisUrl . '/deleteData/' . $item->kategori_reg_id) }}')">
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
     {{ $kategoriRegulasi->appends(['search' => $search])->links() }}
 </div>