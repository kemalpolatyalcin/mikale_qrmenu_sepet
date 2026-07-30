<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikale | Restoran Seçimi</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-[#F9F8F3] min-h-screen flex items-center justify-center p-6 font-poppins">
    <div class="w-full max-w-4xl">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight mb-2">Restoran Seçin</h1>
            <p class="text-sm text-gray-500">Yapılandırmak ve yönetmek istediğiniz restoranı belirleyin.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($restaurants as $res)
                <form action="{{ route('admin.restaurants.select') }}" method="POST" id="select-form-{{ $res->id }}" class="m-0">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $res->id }}">
                    <div onclick="document.getElementById('select-form-{{ $res->id }}').submit()"
                         class="bg-white p-8 rounded-[2rem] border border-gray-100 hover:border-[#8C6C47] shadow-sm hover:shadow-xl hover:scale-[1.02] cursor-pointer transition-all duration-300 flex flex-col h-full">
                        @if($res->logo_url)
                            <img src="{{ asset($res->logo_url) }}" class="w-16 h-16 object-contain rounded-2xl border border-gray-100 bg-white mb-6" alt="Logo">
                        @else
                            <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center text-[#8C6C47] text-2xl font-bold mb-6">
                                {{ mb_substr($res->name, 0, 1, 'UTF-8') }}
                            </div>
                        @endif
                        <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $res->name }}</h2>
                        <p class="text-xs text-gray-400 mb-6 flex-1">Bu restoranın menüsünü, kategorilerini, ürünlerini ve masalarını yapılandırmak için devam edin.</p>
                        <div class="flex items-center gap-2 text-xs font-semibold text-[#8C6C47] hover:text-[#1C1C1C] transition-colors">
                            <span>Yönetmeye Başla</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </div>
                </form>
            @endforeach
        </div>
    </div>
</body>
</html>
