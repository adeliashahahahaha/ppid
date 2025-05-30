<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\KetentuanPelaporan;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\KategoriForm\KategoriFormModel;
use Modules\Sisfo\App\Models\SistemInformasi\KetentuanPelaporan\KetentuanPelaporanModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KetentuanPelaporanController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Ketentuan Pelaporan';
    public $pagename = 'SistemInformasi/KetentuanPelaporan';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Ketentuan Pelaporan',
            'list' => ['Home', 'Pengaturan Ketentuan Pelaporan']
        ];

        $page = (object) [
            'title' => 'Daftar Ketentuan Pelaporan'
        ];

        $activeMenu = 'ketentuanpelaporan';
        
        // Gunakan pagination dan pencarian
        $ketentuanPelaporan = KetentuanPelaporanModel::selectData(10, $search);

        return view("sisfo::SistemInformasi/KetentuanPelaporan.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'ketentuanPelaporan' => $ketentuanPelaporan,
            'search' => $search
        ]);
    }

    // Update getData untuk mendukung pencarian
    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $ketentuanPelaporan = KetentuanPelaporanModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('sisfo::SistemInformasi/KetentuanPelaporan.data', compact('ketentuanPelaporan', 'search'))->render();
        }
        
        return redirect()->route('ketentuan-pelaporan.index');
    }

    public function addData()
    {
        $kategoriForms = KategoriFormModel::where('isDeleted', 0)->get();

        return view("sisfo::SistemInformasi/KetentuanPelaporan.create", [
            'kategoriForms' => $kategoriForms
        ]);
    }

    public function createData(Request $request)
    {
        try {
            KetentuanPelaporanModel::validasiData($request);
            $result = KetentuanPelaporanModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Ketentuan pelaporan berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat ketentuan pelaporan');
        }
    }

    public function editData($id)
    {
        $kategoriForms = KategoriFormModel::where('isDeleted', 0)->get();
        $ketentuanPelaporan = KetentuanPelaporanModel::findOrFail($id);

        return view("sisfo::SistemInformasi/KetentuanPelaporan.update", [
            'kategoriForms' => $kategoriForms,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            KetentuanPelaporanModel::validasiData($request);
            $result = KetentuanPelaporanModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Ketentuan pelaporan berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui ketentuan pelaporan');
        }
    }

    public function detailData($id)
    {
        $ketentuanPelaporan = KetentuanPelaporanModel::with('PelaporanKategoriForm')->findOrFail($id);

        return view("sisfo::SistemInformasi/KetentuanPelaporan.detail", [
            'ketentuanPelaporan' => $ketentuanPelaporan,
            'title' => 'Detail Ketentuan Pelaporan'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $ketentuanPelaporan = KetentuanPelaporanModel::with('PelaporanKategoriForm')->findOrFail($id);

            return view("sisfo::SistemInformasi/KetentuanPelaporan.delete", [
                'ketentuanPelaporan' => $ketentuanPelaporan
            ]);
        }

        try {
            $result = KetentuanPelaporanModel::deleteData($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Ketentuan pelaporan berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus ketentuan pelaporan');
        }
    }

    // Method untuk upload gambar
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $file = $request->file('image');

            if (!$file) {
                return $this->jsonError(
                    new \Exception('Tidak ada file yang diunggah'),
                    '',
                    400
                );
            }

            // Generate nama file unik dan simpan
            $fileName = 'ketentuan_pelaporan/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public', $fileName);

            return $this->jsonSuccess(
                ['url' => asset('storage/' . $fileName)],
                'Gambar berhasil diunggah'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    // Method untuk menghapus gambar
    public function removeImage(Request $request)
    {
        try {
            $request->validate([
                'url' => 'required|string'
            ]);

            $imageUrl = $request->input('url');

            // Extract filename dari full URL
            $pathInfo = parse_url($imageUrl);
            $path = $pathInfo['path'] ?? '';
            $storagePath = str_replace('/storage/', '', $path);

            if (!empty($storagePath)) {
                // Logika untuk menghapus file
                $filePath = storage_path('app/public/' . $storagePath);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                return $this->jsonSuccess(
                    null,
                    'Gambar berhasil dihapus'
                );
            } else {
                return $this->jsonError(
                    new \Exception('Path gambar tidak valid'),
                    '',
                    400
                );
            }
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}