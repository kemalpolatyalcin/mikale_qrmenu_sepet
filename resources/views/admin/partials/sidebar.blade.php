<aside class="w-64 bg-white shadow-xl hidden md:flex flex-col justify-between z-20 shrink-0">
    <div>
        <div class="h-24 flex items-center justify-center border-b border-gray-100 mb-6">
            <span class="font-allison text-6xl text-[#1C1C1C] mt-4 sidebar-restaurant-letter">{{ isset($activeRestaurant) ? substr($activeRestaurant->name, 0, 1) : (substr($siteSettings['restaurant_name'] ?? 'M', 0, 1)) }}</span>
            <span class="text-xl font-bold ml-2 tracking-widest text-[#1C1C1C] sidebar-restaurant-name">{{ isset($activeRestaurant) ? $activeRestaurant->name : ($siteSettings['restaurant_name'] ?? 'MIKALE') }}</span>
        </div>
        @if(session('is_developer') && isset($restaurantsList) && $restaurantsList->count() > 0)
        <div class="px-4 mb-4">
            <form action="{{ route('admin.restaurants.select') }}" method="POST" id="restaurant-select-form">
                @csrf
                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider block mb-1">Aktif Restoran</label>
                <select name="restaurant_id" onchange="document.getElementById('restaurant-select-form').submit()" 
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 focus:outline-none focus:border-[#8C6C47] transition-all">
                    @foreach($restaurantsList as $res)
                        <option value="{{ $res->id }}" {{ isset($activeRestaurant) && $activeRestaurant->id == $res->id ? 'selected' : '' }}>
                            {{ $res->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
        <nav class="px-4 space-y-2">
            <a href="{{ url('/') }}" target="_blank"
                class="flex items-center gap-3 px-4 py-3 text-[#8C6C47] bg-amber-50 rounded-xl mb-4 border border-amber-100">
                <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i> <span>Menüyü Gör</span>
            </a>
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i> <span>Gösterge Paneli</span>
            </a>
            <a href="{{ route('admin.categories') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.categories') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-layer-group w-5 text-center"></i> <span>Kategoriler</span>
            </a>
            <a href="{{ route('admin.products') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.products') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-utensils w-5 text-center"></i> <span>Ürünler</span>
            </a>
            <a href="{{ route('admin.orders') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.orders') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-bell-concierge w-5 text-center"></i> <span>Aktif Siparişler</span>
            </a>
            <a href="{{ route('admin.tables') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.tables') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-qrcode w-5 text-center"></i> <span>Masalar ve QR</span>
            </a>
            @if(session('is_developer'))
                <a href="{{ route('admin.developer.restaurants') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.developer.restaurants') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-hotel w-5 text-center"></i> <span>Restoranlar</span>
                </a>
            @endif
            <a href="{{ route('admin.settings') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-gear w-5 text-center"></i> <span>Ayarlar</span>
            </a>
        </nav>
    </div>
    <div class="p-4 border-t border-gray-100">
        <form action="{{ route('logout') }}" method="POST" class="mb-4">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> Çıkış Yap
            </button>
        </form>
        <div class="text-center text-[10px] text-gray-400 font-medium tracking-wide">
            Powered by <strong class="text-gray-600">Mikale QR</strong> v1.0
        </div>
    </div>
</aside>