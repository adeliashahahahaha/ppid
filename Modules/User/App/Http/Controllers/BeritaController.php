<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Services\JwtTokenService;

class BeritaController extends Controller
{
    protected $jwtTokenService;
    protected $baseUrl;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
        $this->baseUrl = config('BASE_URL', env('BASE_URL'));
    }

    private function makeAuthenticatedRequest($endpoint)
    {
        try {
            // Get active token
            $tokenData = $this->jwtTokenService->getActiveToken();
            
            // Make request with token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenData['token']
            ])->get($this->baseUrl . '/api/' . $endpoint);

            // Check if token expired
            if ($response->status() === 401) {
                // Generate new token and retry
                $tokenData = $this->jwtTokenService->generateSystemToken();
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $tokenData['token']
                ])->get($this->baseUrl . '/api/' . $endpoint);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function index(Request $request)
    {
        try {
            // Ambil halaman saat ini dari request, default ke 1
            $page = $request->get('page', 1);
            
            // Validasi nomor halaman
            if (!is_numeric($page) || $page < 1) {
                Log::warning('Nomor halaman tidak valid', [
                    'page' => $page
                ]);
    
                return view('user::404', [
                    'message' => 'Halaman tidak ditemukan'
                ]);
            }
    
           // Ambil data dari API dengan authenticated request
        $beritaResponse = $this->makeAuthenticatedRequest('public/getDataBerita?' . http_build_query([
            'page' => $page
        ]));
    
            $beritaData = $this->fetchBeritaData($beritaResponse);
    
            // Validasi data berita
            if (empty($beritaData['items']) && $page > 1) {
                Log::warning('Halaman tidak ditemukan', [
                    'page' => $page
                ]);
    
                return view('user::404', [
                    'message' => 'Halaman tidak ditemukan'
                ]);
            }
    
            // Jika ini adalah request AJAX, kembalikan hanya data JSON
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('user::partials.berita-list', [
                        'beritaMenus' => $beritaData['items'],
                    ])->render(),
                    'pagination' => $beritaData['pagination'],
                ]);
            }
    
            return view('user::berita', [
                'beritaMenus' => $beritaData['items'],
                'pagination' => $beritaData['pagination'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            if ($request->ajax()) {
                return response()->json([
                    'html' => '<p class="text-center">Terjadi kesalahan saat mengambil data.</p>',
                    'pagination' => null
                ]);
            }
    
            return view('user::404', [
                'message' => 'Terjadi kesalahan saat mengambil data'
            ]);
        }
    }

    private function fetchBeritaData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pengumuman gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [
                'items' => [],
                'pagination' => null,
            ];
        }

        return $this->processBeritaData($response->json('data'));
    }

    private function processBeritaData($data)
    {
        $result = [];

        $beritaList = $data['data'] ?? [];

        foreach ($beritaList as $item) {
            $result[] = [
                'berita_id' => $item['berita_id'] ?? null,
                'kategori' => $item['kategori'] ?? 'Berita',
                'judul' => $item['judul'] ?? 'Tanpa Judul',
                'slug' => $item['slug'] ?? null,
                'thumbnail' => $item['thumbnail'] ?? null,
                'deskripsiThumbnail' => $item['deskripsiThumbnail'] ?? null,
                'tanggal' => $item['tanggal'] ?? null,
                'url_selengkapnya' => $item['url_selengkapnya'] ?? null,
            ];
        }

        return [
            'items' => $result,
            'pagination' => [
                'current_page' => $data['current_page'] ?? 1,
                'total_pages' => $data['total_pages'] ?? 1,
                'next_page_url' => $data['next_page_url'] ?? null,
                'prev_page_url' => $data['prev_page_url'] ?? null,
            ]
        ];
    }


    public function detail($slug, $encryptedId)
    {
        try {
            // Dekripsi ID dari URL
            $beritaId = Crypt::decryptString(urldecode($encryptedId));

            // Ambil data detail berita dari API berdasarkan ID
            $detailResponse = $this->makeAuthenticatedRequest("public/getDetailBeritaById/{$slug}/{$beritaId}");

            $detailData = $this->fetchBeritaDetail($detailResponse);

            // Validasi data kosong atau slug tidak cocok
            if (empty($detailData) || $detailData['slug'] !== $slug) {
                return view('user::404', [
                    'message' => 'Laman Tidak Ditemukan',
                ]);
            }

            return view('user::berita-detail', [
                'beritaDetail' => $detailData
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil detail berita dari API', [
                'encrypted_id' => $encryptedId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Jika gagal dekripsi atau terjadi error lain, arahkan ke 404
            return view('user::404', [
                'message' => 'Laman Tidak Ditemukan',
            ]);
        }
    }


    private function fetchBeritaDetail($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Detail Berita gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return null;
        }

        return $this->processBeritaDetail($response->json('data'));
    }

    private function processBeritaDetail($data)
    {
        if (empty($data)) {
            return null;
        }

        return [
            'berita_id'         => $data['berita_id'] ?? null,
            'kategori'          => $data['kategori'] ?? 'Berita',
            'judul'             => $data['judul'] ?? 'Tanpa Judul',
            'slug'              => $data['slug'] ?? null,
            'thumbnail'         => $data['thumbnail'] ?? null,
            'deskripsiThumbnail' => $data['deskripsiThumbnail'] ?? null,
            'tanggal'           => $data['tanggal'] ?? null,
            'konten'            => $data['konten'] ?? null
        ];
    }
}
