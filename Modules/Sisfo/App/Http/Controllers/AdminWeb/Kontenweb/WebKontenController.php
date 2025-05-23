<?php
namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\Kontenweb;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\WebKontenModel;
use Modules\Sisfo\App\Models\Website\WebKontenImagesModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class WebKontenController extends Controller
{
    use TraitsController;

    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebKontenModel::createData($request);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebKontenModel::updateData($request, $id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function delete($id)
    {
        if (request()->ajax()) {
            $result = WebKontenModel::deleteData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('public/konten');
            return response()->json(['location' => Storage::url($path)]);
        }
        return response()->json(['error' => 'Gagal mengupload gambar'], 500);
    }

    public function deleteImage($id)
    {
        WebKontenImagesModel::deleteImage($id);
        return response()->json(['status' => true, 'message' => 'Gambar berhasil dihapus']);
    }
}