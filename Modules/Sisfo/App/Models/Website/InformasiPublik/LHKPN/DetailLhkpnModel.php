<?php
namespace Modules\Sisfo\App\Models\Website\InformasiPublik\LHKPN;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DetailLhkpnModel extends Model
{
    use TraitsModel;

    protected $table = 't_detail_lhkpn';
    protected $primaryKey = 'detail_lhkpn_id';
    protected $fillable = [
        'fk_m_lhkpn',
        'dl_nama_karyawan',
        'dl_file_lhkpn'
    ];

    public function lhkpn()
    {
        return $this->belongsTo(LhkpnModel::class, 'fk_m_lhkpn', 'lhkpn_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::with('lhkpn')
            ->where('isDeleted', 0);
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('dl_nama_karyawan', 'like', "%{$search}%")
                    ->orWhere('dl_file_lhkpn', 'like', "%{$search}%")
                    ->orWhereHas('lhkpn', function ($subQuery) use ($search) {
                        $subQuery->where('lhkpn_tahun', 'like', "%{$search}%");
                    });
            });
        }
    
        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        $lhkpnFile = self::uploadFile(
            $request->file('dl_file_lhkpn'),
            'lhkpn'
        );
        try {
            DB::beginTransaction();
    
            $data = $request->t_detail_lhkpn;
            
            // Cek apakah nama karyawan sudah ada di tahun yang sama
            $isUsed = self::where('dl_nama_karyawan', $data['dl_nama_karyawan'])
                ->where('fk_m_lhkpn', $data['fk_m_lhkpn'])
                ->where('isDeleted', 0)
                ->exists();
    
            if ($isUsed) {
                DB::rollBack();
                throw new \Exception('Maaf, nama karyawan sudah ada pada tahun LHKPN yang sama');
            }
    
            // Jika file diupload
            if ($lhkpnFile) {
                $data['dl_file_lhkpn'] = $lhkpnFile;
            }
            $detailLhkpn = self::create($data);
    
            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $detailLhkpn->detail_lhkpn_id,
                $detailLhkpn->dl_nama_karyawan
            );
            $result = self::responFormatSukses($detailLhkpn, 'Detail LHKPN berhasil dibuat');
    
            DB::commit();
            return $result;
    
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($lhkpnFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($lhkpnFile);
            return self::responFormatError($e, 'Gagal membuat detail LHKPN');
        }
    }
    
    public static function updateData($request, $id)
    {
        $lhkpnFile = self::uploadFile(
            $request->file('dl_file_lhkpn'),
            'lhkpn'
        );
        try {
            DB::beginTransaction();
    
            $detailLhkpn = self::findOrFail($id);
            $data = $request->t_detail_lhkpn;
            
            // Cek apakah nama karyawan sudah ada di tahun yang sama, kecuali record saat ini
            $isUsed = self::where('dl_nama_karyawan', $data['dl_nama_karyawan'])
                ->where('fk_m_lhkpn', $data['fk_m_lhkpn'])
                ->where('detail_lhkpn_id', '!=', $id)
                ->where('isDeleted', 0)
                ->exists();
    
            if ($isUsed) {
                DB::rollBack();
                throw new \Exception('Maaf, nama karyawan sudah ada pada tahun LHKPN yang sama');
            }
    
            // Jika file diupload
            if ($lhkpnFile) {
                // Hapus file lama jika ada
                if ($detailLhkpn->dl_file_lhkpn) {
                    self::removeFile($detailLhkpn->dl_file_lhkpn);
                }
    
                $data['dl_file_lhkpn'] = $lhkpnFile;
            }
            $detailLhkpn->update($data);
    
            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $detailLhkpn->detail_lhkpn_id,
                $detailLhkpn->dl_nama_karyawan
            );
            $result = self::responFormatSukses($detailLhkpn, 'Detail LHKPN berhasil diperbarui');
    
            DB::commit();
            return $result;
    
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($lhkpnFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($lhkpnFile);
            return self::responFormatError($e, 'Gagal memperbarui detail LHKPN');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $detailLhkpn = self::findOrFail($id);
              // Hapus file  jika ada
              if ($detailLhkpn->dl_file_lhkpn) {
                self::removeFile($detailLhkpn->dl_file_lhkpn);
            }

            $detailLhkpn->delete();

            TransactionModel::createData(
                'DELETED',
                $detailLhkpn->detail_lhkpn_id,
                $detailLhkpn->dl_nama_karyawan
            );

            DB::commit();

            return self::responFormatSukses($detailLhkpn, 'Detail LHKPN berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus detail LHKPN');
        }
    }

    public static function detailData($id)
    {
        return self::with('lhkpn')->findOrFail($id);
    }

    public static function validasiData($request, $id = null)
    {
        $rules = [
            't_detail_lhkpn.fk_m_lhkpn' => 'required|exists:m_lhkpn,lhkpn_id',
            't_detail_lhkpn.dl_nama_karyawan' => 'required|max:100',
        ];

        // Validasi file
        if ($id) {
            // Update - file tidak wajib
            $rules['dl_file_lhkpn'] = 'nullable|file|max:2560|mimes:pdf';
        } else {
            // Create - file wajib
            $rules['dl_file_lhkpn'] = 'required|file|max:2560|mimes:pdf';
        }

        $messages = [
            't_detail_lhkpn.fk_m_lhkpn.required' => 'Tahun LHKPN wajib dipilih',
            't_detail_lhkpn.fk_m_lhkpn.exists' => 'LHKPN tidak valid',
            't_detail_lhkpn.dl_nama_karyawan.required' => 'Nama karyawan wajib diisi',
            't_detail_lhkpn.dl_nama_karyawan.max' => 'Nama karyawan maksimal 100 karakter',
            'dl_file_lhkpn.required' => 'File LHKPN wajib diupload',
            'dl_file_lhkpn.file' => 'Upload harus berupa file',
            'dl_file_lhkpn.max' => 'Ukuran file maksimal 2.5MB',
            'dl_file_lhkpn.mimes' => 'Format file harus PDF',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}