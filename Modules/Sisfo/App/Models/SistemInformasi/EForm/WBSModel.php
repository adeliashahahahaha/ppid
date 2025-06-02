<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\EForm;

use Modules\Sisfo\App\Models\Log\NotifAdminModel;
use Modules\Sisfo\App\Models\Log\NotifVerifikatorModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WBSModel extends Model
{
    use TraitsModel;

    protected $table = 't_wbs';
    protected $primaryKey = 'wbs_id';
    protected $fillable = [
        'wbs_kategori_aduan',
        'wbs_bukti_aduan',
        'wbs_nama_tanpa_gelar',
        'wbs_nik_pengguna',
        'wbs_upload_nik_pengguna',
        'wbs_email_pengguna',
        'wbs_no_hp_pengguna',
        'wbs_jenis_laporan',
        'wbs_yang_dilaporkan',
        'wbs_jabatan',
        'wbs_waktu_kejadian',
        'wbs_lokasi_kejadian',
        'wbs_kronologis_kejadian',
        'wbs_bukti_pendukung',
        'wbs_catatan_tambahan',
        'wbs_status',
        'wbs_jawaban',
        'wbs_alasan_penolakan',
        'wbs_sudah_dibaca',
        'wbs_tanggal_dibaca',
        'wbs_review',
        'wbs_tanggal_review',
        'wbs_tanggal_dijawab',
        'wbs_verif_isDeleted'
        
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($request)
    {
        $uploadNikPelaporFile = self::uploadFile(
            $request->file('wbs_upload_nik_pengguna'),
            'wbs_identitas_pelapor'
        );

        $buktiPendukungFile = self::uploadFile(
            $request->file('wbs_bukti_pendukung'),
            'wbs_bukti_pendukung'
        );

        $buktiAduanFile = self::uploadFile(
            $request->file('wbs_bukti_aduan'),
            'wbs_bukti_aduan'
        );

        try {
            DB::beginTransaction();

            $data = $request->t_wbs;
            $userLevel = Auth::user()->level->hak_akses_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            // Jika user RPN, gunakan data dari auth
            if ($userLevel === 'RPN') {
                $data['wbs_no_hp_pengguna'] = Auth::user()->no_hp_pengguna;
                $data['wbs_email_pengguna'] = Auth::user()->email_pengguna;
                $data['wbs_nik_pengguna'] = Auth::user()->nik_pengguna;
                $data['wbs_upload_nik_pengguna'] = Auth::user()->upload_nik_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['wbs_upload_nik_pengguna'] = $uploadNikPelaporFile;
                $data['wbs_bukti_aduan'] = $buktiAduanFile;
            }

            $data['wbs_kategori_aduan'] = $kategoriAduan;
            $data['wbs_bukti_pendukung'] = $buktiPendukungFile;
            $data['wbs_status'] = 'Masuk';

            $saveData = self::create($data);
            $wbsId = $saveData->wbs_id;

            // Create notifications
            $notifMessage = "{$saveData->wbs_nama_tanpa_gelar} Mengajukan Whistle Blowing System";
            NotifAdminModel::createData($wbsId, $notifMessage);
            NotifVerifikatorModel::createData($wbsId, $notifMessage);

            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->wbs_id,
                $saveData->wbs_jenis_laporan
            );

            $result = self::responFormatSukses($saveData, 'Whistle Blowing System berhasil diajukan.');

            DB::commit();

            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($uploadNikPelaporFile);
            self::removeFile($buktiPendukungFile);
            self::removeFile($buktiAduanFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($uploadNikPelaporFile);
            self::removeFile($buktiPendukungFile);
            self::removeFile($buktiAduanFile);
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan Whistle Blowing System');
        }
    }

    public static function validasiData($request)
    {
        // Dapatkan level user saat ini
        $userLevel = Auth::user()->level->hak_akses_kode;

        // rules validasi dasar untuk Whistle Blowing System
        $rules = [
            't_wbs.wbs_nama_tanpa_gelar' => 'required',
            't_wbs.wbs_jenis_laporan' => 'required',
            't_wbs.wbs_yang_dilaporkan' => 'required',
            't_wbs.wbs_jabatan' => 'required',
            't_wbs.wbs_waktu_kejadian' => 'required|date',
            't_wbs.wbs_lokasi_kejadian' => 'required',
            't_wbs.wbs_kronologis_kejadian' => 'required',
            'wbs_bukti_pendukung' => 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx,mp4,avi,mov,wmv,3gp,mp3,wav,ogg,m4a|max:100000',
        ];

        // message validasi
        $message = [
            't_wbs.wbs_nama_tanpa_gelar.required' => 'Nama wajib diisi',
            't_wbs.wbs_jenis_laporan.required' => 'Jenis laporan wajib diisi',
            't_wbs.wbs_yang_dilaporkan.required' => 'Yang dilaporkan wajib diisi',
            't_wbs.wbs_jabatan.required' => 'Jabatan wajib diisi',
            't_wbs.wbs_waktu_kejadian.required' => 'Waktu kejadian wajib diisi',
            't_wbs.wbs_waktu_kejadian.date' => 'Format tanggal tidak valid',
            't_wbs.wbs_lokasi_kejadian.required' => 'Lokasi kejadian wajib diisi',
            't_wbs.wbs_kronologis_kejadian.required' => 'Kronologis kejadian wajib diisi',
            'wbs_bukti_pendukung.required' => 'Upload Bukti Aduan penginput wajib diisi',
            'wbs_bukti_pendukung.file' => 'Bukti pendukung harus berupa file',
            'wbs_bukti_pendukung.mimes' => 'Format file tidak didukung. Format yang didukung: PDF, gambar, dokumen, video (MP4, AVI, MOV, WMV, 3GP), atau audio (MP3, WAV, OGG, M4A)',
            'wbs_bukti_pendukung.max' => 'Ukuran file tidak boleh lebih dari 100MB',
        ];

        // Tambahkan validasi khusus untuk ADM
        if ($userLevel === 'ADM') {
            $rules['t_wbs.wbs_nik_pengguna'] = 'required|numeric|digits:16';
            $rules['t_wbs.wbs_email_pengguna'] = 'required|email';
            $rules['t_wbs.wbs_no_hp_pengguna'] = 'required';
            $rules['wbs_upload_nik_pengguna'] = 'required|image|max:10240';
            $rules['wbs_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
            $message['t_wbs.wbs_nik_pengguna.required'] = 'NIK wajib diisi';
            $message['t_wbs.wbs_nik_pengguna.numeric'] = 'NIK harus berupa angka';
            $message['t_wbs.wbs_nik_pengguna.digits'] = 'NIK harus 16 digit';
            $message['t_wbs.wbs_email_pengguna.required'] = 'Email wajib diisi';
            $message['t_wbs.wbs_email_pengguna.email'] = 'Format email tidak valid';
            $message['t_wbs.wbs_no_hp_pengguna.required'] = 'Nomor HP wajib diisi';
            $message['wbs_upload_nik_pengguna.required'] = 'Upload NIK wajib diisi';
            $message['wbs_upload_nik_pengguna.image'] = 'File harus berupa gambar';
            $message['wbs_upload_nik_pengguna.max'] = 'Ukuran file tidak boleh lebih dari 10MB';
            $message['wbs_bukti_aduan.required'] = 'Bukti aduan wajib diupload untuk Admin';
            $message['wbs_bukti_aduan.file'] = 'Bukti aduan harus berupa file';
            $message['wbs_bukti_aduan.mimes'] = 'Format file bukti aduan tidak valid';
            $message['wbs_bukti_aduan.max'] = 'Ukuran file bukti aduan maksimal 10MB';
        }

        // Lakukan validasi
        $validator = Validator::make($request->all(), $rules, $message);

        // Lemparkan exception jika validasi gagal
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function getTimeline()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getTimelineByKategoriForm('Whistle Blowing System');
    }

    public static function getKetentuanPelaporan()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getKetentuanPelaporanByKategoriForm('Whistle Blowing System');
    }

    public static function hitungJumlahVerifikasi()
    {
        // Hanya menghitung verifikasi untuk WBS
        return self::where('wbs_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('wbs_verif_isDeleted', 0)
            ->whereNull('wbs_sudah_dibaca')
            ->count();
    }

    public static function getDaftarVerifikasi()
    {
        // Mengambil daftar WBS untuk verifikasi
        return self::where('isDeleted', 0)
            ->where('wbs_verif_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validasiDanSetujuiPermohonan()
    {
        // Validasi status
        if ($this->wbs_status !== 'Masuk') {
            throw new \Exception('Pengajuan Whistle Blowing System sudah diverifikasi sebelumnya');
        }

        // Update status menjadi Verifikasi
        $this->wbs_status = 'Verifikasi';
        $this->wbs_review = session('alias') ?? 'System';
        $this->wbs_tanggal_review = now();
        $this->save();

        return $this;
    }    

    public function validasiDanTolakPermohonan($alasanPenolakan)
    {
        // Validasi alasan penolakan
        $validator = Validator::make(
            ['alasan_penolakan' => $alasanPenolakan],
            ['alasan_penolakan' => 'required|string|max:255'],
            [
                'alasan_penolakan.required' => 'Alasan penolakan wajib diisi',
                'alasan_penolakan.max' => 'Alasan penolakan maksimal 255 karakter'
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validasi status
        if ($this->wbs_status !== 'Masuk') {
            throw new \Exception('Pengajuan sudah diverifikasi sebelumnya');
        }

        // Update status menjadi Ditolak
        $this->wbs_status = 'Ditolak';
        $this->wbs_alasan_penolakan = $alasanPenolakan;
        $this->wbs_review = session('alias') ?? 'System';
        $this->wbs_tanggal_review = now();
        $this->save();

        return $this;
    }

    public function validasiDanTandaiDibaca()
    {
        // Validasi status permohonan
        if (!in_array($this->wbs_status, ['Verifikasi', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak pengajuan ini terlebih dahulu');
        }

        // Tandai sebagai dibaca
        $this->wbs_sudah_dibaca = session('alias') ?? 'System';
        $this->wbs_tanggal_dibaca = now();
        $this->save();

        return $this;
    }

    public function validasiDanHapusPermohonan()
    {
        // Validasi status dibaca
        if (empty($this->wbs_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->wbs_verif_isDeleted = 1;
        $this->wbs_tanggal_dijawab = now();
        $this->save();

        return $this;
    }
}
