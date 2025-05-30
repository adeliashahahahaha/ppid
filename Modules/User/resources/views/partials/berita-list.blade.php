{{-- Save this as 'resources/views/modules/user/partials/berita-list.blade.php' --}}
@forelse ($beritaMenus as $berita)
    <article class="d-flex align-items-start m-5" style="gap: 20px;">
        <figure class="me-3" style="flex: 0 0 50%;">
            <img src="{{ $berita['thumbnail'] ?? asset('img/default-thumbnail.jpg') }}"
                 class="img-berita" alt="Gambar Berita"
                 style="width: 80%; height: auto;">
        </figure>
        <div>
            <h4 class="fw-bold pt-0 pb-2">{{ $berita['judul'] }}</h4>
            <time class="text-muted pt-2 pb-2" datetime="{{ $berita['tanggal'] }}">{{ \Carbon\Carbon::parse($berita['tanggal'])->translatedFormat('d F Y') }}</time>
            <p class="pt-2">{{ $berita['deskripsiThumbnail'] }}</p>
            @if($berita['url_selengkapnya'])
                @php
                    // Enkripsi berita_id dan lakukan URL encode untuk menghindari karakter khusus
                    $encryptedId = urlencode(Crypt::encryptString($berita['berita_id']));
                @endphp
                <a class="read-moree"
                   href="{{ url('berita-detail/'.$berita['slug'].'/'.$encryptedId) }}"
                   target="_blank">
                   Baca selengkapnya
                </a>
            @endif
        </div>
    </article>
@empty
    <p class="text-center">Tidak ada berita tersedia saat ini.</p>
@endforelse
