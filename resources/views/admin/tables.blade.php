@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Masalar ve QR Kodlar</h1>
                <p class="text-sm text-gray-500">Restoranınızdaki masaları yönetin ve müşterileriniz için sipariş QR kodları
                    oluşturun.</p>
            </div>
            <form action="{{ route('admin.tables.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="name" placeholder="Örn: Teras 1, Masa 5" required
                    class="bg-white border border-gray-200 rounded-xl px-4 py-2 focus:outline-none focus:border-[#8C6C47] text-sm min-w-[200px]">
                <button type="submit"
                    class="bg-[#1C1C1C] hover:bg-[#8C6C47] text-white font-medium py-2 px-4 rounded-xl transition-colors shadow-sm text-sm whitespace-nowrap">
                    <i class="fa-solid fa-plus mr-1"></i> Masa Ekle
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-100 flex items-center gap-3 text-sm">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($tables as $table)
                <div
                    class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 flex flex-col items-center relative overflow-hidden group">

                    <form action="{{ route('admin.tables.delete', $table->id) }}" method="POST"
                        class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity"
                        onsubmit="return confirm('Bu masayı silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-8 h-8 bg-red-50 text-red-500 rounded-full flex items-center justify-center hover:bg-red-100 transition-colors">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>

                    <div
                        class="w-16 h-16 bg-amber-50 text-[#8C6C47] rounded-full flex items-center justify-center text-2xl mb-4 border border-amber-100">
                        <i class="fa-solid fa-chair"></i>
                    </div>

                    <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $table->name }}</h3>
                    <p class="text-xs text-gray-400 mb-6 font-mono bg-gray-50 px-2 py-1 rounded">Masa Kodu: {{ $table->token }}
                    </p>

                    <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm mb-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(url('/?masa=' . $table->token)) }}" alt="QR Code" class="w-[120px] h-[120px]">
                    </div>

                    <a href="{{ url('/?masa=' . $table->token) }}" target="_blank"
                        class="text-xs font-medium text-[#8C6C47] hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-link"></i> Linki Test Et
                    </a>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-[2rem] p-12 text-center border border-gray-100">
                    <div
                        class="w-20 h-20 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Henüz Masa Yok</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto">Sipariş almaya başlamak için sağ üstteki formu kullanarak
                        ilk masanızı oluşturun. QR kodlar otomatik üretilecektir.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection