<div id="mobile-admin-menu"
    class="fixed inset-0 z-50 transform translate-x-full transition-transform duration-300 md:hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        onclick="document.getElementById('mobile-admin-menu').classList.add('translate-x-full')"></div>
    <div class="absolute top-0 right-0 w-64 h-full bg-white shadow-xl flex flex-col justify-between py-6 px-4">
        <div>
            <div class="flex justify-between items-center mb-8 px-2 border-b border-gray-100 pb-4">
                <span class="font-serif text-xl font-bold tracking-widest text-[#1C1C1C]">MIKALE</span>
                <button onclick="document.getElementById('mobile-admin-menu').classList.add('translate-x-full')"
                    class="text-gray-500 text-xl focus:outline-none"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <nav class="space-y-3">
                <a href="{{ url('/') }}" target="_blank"
                    class="flex items-center gap-3 px-4 py-3 text-[#8C6C47] bg-amber-50 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i> Menüyü Görüntüle
                </a>
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Gösterge Paneli
                </a>
                <a href="{{ route('admin.categories') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.categories') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-layer-group w-5 text-center"></i> Kategoriler
                </a>
                <a href="{{ route('admin.products') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.products') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-utensils w-5 text-center"></i> Ürünler
                </a>
                <a href="{{ route('admin.orders') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.orders') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-bell-concierge w-5 text-center"></i> Aktif Siparişler
                </a>
                <a href="{{ route('admin.tables') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.tables') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-qrcode w-5 text-center"></i> Masalar ve QR
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#8C6C47] text-white shadow-md mt-4' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47] mt-4' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-gear w-5 text-center"></i> Ayarlar
                </a>
            </nav>
        </div>

        <div class="mt-4 border-t border-gray-100 pt-4">
            <form action="{{ route('logout') }}" method="POST" class="mb-4">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> Çıkış Yap
                </button>
            </form>
            <div class="text-center text-[9px] text-gray-400 font-medium tracking-wide">
                Powered by <strong class="text-gray-600">Mikale QR Menu</strong>
            </div>
        </div>
    </div>
</div>