<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\EForm;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormPiOrganisasiModel extends Model
{
    use TraitsModel;

    protected $table = 't_form_pi_organisasi';
    protected $primaryKey = 'form_pi_organisasi_id';
    protected $fillable = [
        'pi_nama_organisasi',
        'pi_no_telp_organisasi',
        'pi_email_atau_medsos_organisasi',
        'pi_nama_narahubung',
        'pi_no_telp_narahubung',
        'pi_identitas_narahubung'
    ];

    // Konstruktor untuk menggabungkan field umum
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($request)
{
    $uploadNikPelaporFile = null;
    
    try {
        // Pindahkan upload file ke dalam try-catch
        if ($request->hasFile('pi_identitas_narahubung')) {
            $uploadNikPelaporFile = self::uploadFile(
                $request->file('pi_identitas_narahubung'),
                'pi_organisasi_identitas'
            );
        } else {
            throw new \Exception('File identitas narahubung wajib diunggah');
        }

        $data = $request->t_form_pi_organisasi;
        $data['pi_identitas_narahubung'] = $uploadNikPelaporFile;
        $saveData = self::create($data);

        $result = [
            'pkField' => 'fk_t_form_pi_organisasi',
            'id' => $saveData->form_pi_organisasi_id,
            'message' => "{$saveData->pi_nama_organisasi} Mengajukan Permohonan Informasi",
        ];
        return $result;
    } catch (\Exception $e) {
        // Hapus file jika terjadi error
        if ($uploadNikPelaporFile) {
            self::removeFile($uploadNikPelaporFile);
        }
        throw $e;
    }
}

    public static function validasiData($request)
{
    // rules validasi untuk form organisasi
    $rules = [
        't_form_pi_organisasi.pi_nama_organisasi' => 'required',
        't_form_pi_organisasi.pi_no_telp_organisasi' => 'required',
        't_form_pi_organisasi.pi_email_atau_medsos_organisasi' => 'required|email',
        't_form_pi_organisasi.pi_nama_narahubung' => 'required',
        't_form_pi_organisasi.pi_no_telp_narahubung' => 'required',
        'pi_identitas_narahubung' => 'required|image|max:10240',
    ];

    // message validasi
    $message = [
        't_form_pi_organisasi.pi_nama_organisasi.required' => 'Nama organisasi wajib diisi',
        't_form_pi_organisasi.pi_no_telp_organisasi.required' => 'Nomor telepon organisasi wajib diisi',
        't_form_pi_organisasi.pi_email_atau_medsos_organisasi.required' => 'Email atau media sosial organisasi wajib diisi',
        't_form_pi_organisasi.pi_email_atau_medsos_organisasi.email' => 'Format email atau media sosial organisasi tidak valid',
        't_form_pi_organisasi.pi_nama_narahubung.required' => 'Nama narahubung wajib diisi',
        't_form_pi_organisasi.pi_no_telp_narahubung.required' => 'Nomor telepon narahubung wajib diisi',
        'pi_identitas_narahubung.required' => 'Identitas narahubung wajib diisi',
        'pi_identitas_narahubung.image' => 'File harus berupa gambar',
        'pi_identitas_narahubung.max' => 'Ukuran file tidak boleh lebih dari 10MB',
    ];

    // Lakukan validasi
    $validator = Validator::make($request->all(), $rules, $message);

    // Lemparkan exception jika validasi gagal
    if ($validator->fails()) {
        throw new ValidationException($validator);
    }
}
}
