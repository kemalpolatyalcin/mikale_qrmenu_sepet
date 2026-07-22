<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikale | Yönetim Paneli</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Allison&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        .font-allison {
            font-family: 'Allison', cursive;
        }

        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F9F8F3] font-poppins flex h-screen overflow-hidden">

    @include('admin.partials.sidebar')

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header
            class="md:hidden bg-white/90 shadow-sm z-30 px-6 py-4 flex justify-between items-center border-b border-gray-100 shrink-0">
            <div class="font-allison text-3xl text-black leading-none">M</div>
            <button onclick="document.getElementById('mobile-admin-menu').classList.remove('translate-x-full')"
                class="text-gray-800 text-2xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>
        </header>

        <header
            class="hidden md:flex h-20 bg-white/80 backdrop-blur-md items-center justify-between px-8 shadow-sm z-10 sticky top-0">
            <h2 class="text-xl font-semibold text-gray-800">Gösterge Paneli</h2>
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-bold border-2 border-[#8C6C47]">
                    {{ substr(Auth::user()->full_name ?? Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="hidden sm:block text-sm">
                    <p class="font-bold text-gray-800">{{ Auth::user()->full_name ?? Auth::user()->name ?? 'Yönetici' }}
                    </p>
                    <p class="text-xs text-gray-500">Admin</p>
                </div>
            </div>
        </header>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div
                        class="w-14 h-14 bg-amber-50 text-[#8C6C47] rounded-xl flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Toplam Kategori</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $categoryCount ?? 0 }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div
                        class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Toplam Ürün</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $productCount ?? 0 }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div
                        class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Aktif Masalar</p>
                        <p class="text-2xl font-bold text-gray-800">12</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div
                        class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Bugünkü Ziyaretçi</p>
                        <p class="text-2xl font-bold text-gray-800">340</p>
                    </div>
                </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center mt-8">
                <i class="fa-solid fa-wand-magic-sparkles text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Sistem Başarıyla Kuruldu</h3>
                <p class="text-gray-500 text-sm max-w-md mx-auto">Sol taraftaki menüyü kullanarak restoranınızın kategorilerini, ürünlerini ve ayarlarını yönetmeye başlayabilirsiniz.</p>
            </div>
        </div>
    </main>

    @include('admin.partials.mobile-menu')

</body>
</html>