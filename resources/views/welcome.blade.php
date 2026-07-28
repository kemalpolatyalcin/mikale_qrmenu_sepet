<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $siteSettings['restaurant_name'] ?? 'Mikale' }} Food Menu</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Allison&family=Playfair+Display:ital,wght@1,600&family=Inter:wght@400;500;600&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#F9F8F3',
                            gold: '#8C6C47',
                            text: '#1C1C1C'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .font-allison {
            font-family: 'Allison', cursive;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .font-sans {
            font-family: 'Inter', sans-serif;
        }

        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes scaleFadeIn {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideRight {
            0% {
                transform: translateX(-100%) translateY(-20px);
                opacity: 0;
            }

            100% {
                transform: translateX(0) translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideLeft {
            0% {
                transform: translateX(100%) translateY(20px);
                opacity: 0;
            }

            100% {
                transform: translateX(0) translateY(0);
                opacity: 1;
            }
        }

        .anim-stripe-top {
            animation: slideRight 1.2s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        .anim-stripe-bottom {
            animation: slideLeft 1.2s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        .anim-logo {
            animation: scaleFadeIn 1.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }

        .page-view {
            display: none;
            animation: scaleFadeIn 0.3s ease-out forwards;
        }

        .page-view.active {
            display: block;
        }

        .modal-container {
            position: fixed;
            z-index: 70;
            background: #FFF;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            will-change: transform;
        }

        @media (max-width: 767px) {
            .modal-container {
                bottom: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border-radius: 0;
                transform: translateY(100%);
                transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            }

            .modal-container.open {
                transform: translateY(0);
            }
        }

        @media (min-width: 768px) {
            .modal-container {
                top: 50%;
                left: 50%;
                width: 100%;
                max-width: 500px;
                height: auto;
                max-height: 90vh;
                border-radius: 1.5rem;
                opacity: 0;
                pointer-events: none;
                transform: translate(-50%, -45%);
                transition: transform 0.3s ease, opacity 0.3s ease;
            }

            .modal-container.open {
                transform: translate(-50%, -50%);
                opacity: 1;
                pointer-events: auto;
            }
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            z-index: 60;
        }

        .overlay.open {
            opacity: 1;
            pointer-events: auto;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
    @livewireStyles
</head>

<body class="bg-[#F9F8F3] md:bg-brand-bg flex md:block justify-center h-[100dvh] md:h-auto items-center font-sans">

    <div
        class="w-full max-w-md md:max-w-none h-[100dvh] md:h-auto md:min-h-screen bg-brand-bg relative overflow-hidden md:overflow-visible flex flex-col shadow-2xl md:shadow-none border border-gray-100 md:border-none shrink-0 md:shrink">

        <div id="splash-screen"
            class="absolute inset-0 z-[100] bg-brand-bg flex justify-center items-center transition-opacity duration-700 ease-in-out">
            <svg class="absolute top-[30%] left-0 w-full h-16 anim-stripe-top text-brand-gold" viewBox="0 0 1440 150"
                preserveAspectRatio="none" fill="currentColor">
                <path d="M0,60 C400,160 1000,-40 1440,60 L1440,85 C1000,-15 400,185 0,85 Z"></path>
            </svg>
            <img src="{{ isset($siteSettings['logo']) && $siteSettings['logo'] != '' ? asset($siteSettings['logo']) : asset('images/oztaylan_logo.jpg') }}" class="relative z-10 anim-logo h-32 w-32 object-contain rounded-full shadow-lg bg-white p-1"
                alt="Logo">
            <svg class="absolute bottom-[30%] left-0 w-full h-16 anim-stripe-bottom text-brand-gold"
                viewBox="0 0 1440 150" preserveAspectRatio="none" fill="currentColor">
                <path d="M0,60 C400,160 1000,-40 1440,60 L1440,85 C1000,-15 400,185 0,85 Z"></path>
            </svg>
        </div>

        <header id="main-header"
            class="hidden justify-between items-center px-6 pt-12 md:pt-6 pb-4 bg-brand-bg border-b border-gray-100">
            <div class="cursor-pointer flex items-center gap-2" onclick="switchView('home')">
                <img src="{{ isset($siteSettings['logo']) && $siteSettings['logo'] != '' ? asset($siteSettings['logo']) : asset('images/oztaylan_logo.jpg') }}" class="h-10 w-10 object-contain rounded-full shadow-sm bg-white p-0.5" alt="Logo">
                <span
                    class="font-serif font-bold text-lg hidden md:block tracking-widest">{{ $siteSettings['restaurant_name'] ?? '' }}</span>
            </div>

            <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-gray-500">
                <button onclick="switchView('home')" class="hover:text-brand-gold transition-colors">Ana Sayfa</button>
                <button onclick="switchView('search')" class="hover:text-brand-gold transition-colors">Menü</button>
                <a href="/admin" target="_blank"
                    class="hover:text-brand-gold transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-user-lock"></i> <span>Admin</span>
                </a>
            </nav>

            <div class="flex items-center gap-4 text-sm font-semibold">
                <div class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs font-bold shadow-sm"><span
                        data-i18n="tableLabel">Masa:</span> <span class="current-table-display">-</span></div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto no-scrollbar bg-brand-bg flex flex-col justify-between">

            <div id="view-home" class="page-view active w-full relative">
                <div class="relative w-full h-[340px] md:h-[60vh] rounded-b-[2rem] overflow-hidden">
                    <img src="{{ isset($siteSettings['cover_image']) && $siteSettings['cover_image'] != '' ? asset($siteSettings['cover_image']) : asset('images/background.jpg') }}"
                        class="w-full h-full object-cover" alt="">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-bg from-5% via-black/40 to-black/30"></div>

                    <div class="absolute top-4 md:top-6 right-6 flex items-center gap-3 z-20">
                        <div
                            class="bg-black/50 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold shadow-sm border border-white/20">
                            <span data-i18n="tableLabel">Masa:</span> <span class="current-table-display">-</span>
                        </div>
                        <div
                            class="bg-white text-black px-1 py-1 rounded text-xs font-bold shadow-sm flex items-center">
                            <button class="btn-lang-tr px-1.5 rounded text-black font-bold"
                                onclick="changeLanguage('tr')">TR</button>
                            <span class="text-gray-300">|</span>
                            <button class="btn-lang-en px-1.5 rounded text-gray-400 font-normal"
                                onclick="changeLanguage('en')">EN</button>
                        </div>
                    </div>

                    <div class="absolute bottom-12 left-0 w-full text-center z-10 px-4">
                        <h1
                            class="text-[36px] md:text-[50px] font-poppins font-normal text-white leading-tight drop-shadow-md">
                            {!! $siteSettings['slogan'] ?? 'Harika Tatlar,<br>Güzel Anılar...' !!}
                        </h1>
                    </div>
                </div>

                <svg class="w-full h-12 text-[#8C6C47] -mt-6 relative z-10 drop-shadow-sm" viewBox="0 0 1440 150"
                    preserveAspectRatio="none" fill="currentColor">
                    <path d="M0,60 C400,160 1000,-40 1440,60 L1440,85 C1000,-15 400,185 0,85 Z"></path>
                </svg>

                <div class="flex flex-col items-center px-8 pt-4 pb-4 text-center bg-brand-bg">
                    <div class="mb-4">
                        <img src="{{ isset($siteSettings['logo']) && $siteSettings['logo'] != '' ? asset($siteSettings['logo']) : asset('images/oztaylan_logo.jpg') }}" class="h-28 w-28 object-contain rounded-full shadow-md bg-white border border-gray-100 p-1" alt="Logo">
                    </div>

                    <p data-i18n="heroDesc"
                        class="text-[18px] md:text-[21px] font-poppins font-normal text-brand-text mb-6 md:mb-10 leading-snug max-w-2xl px-4">
                        Gelenekten ilham alan lezzetleri modern bir dokunuşla sunuyor, her ziyareti özel bir anıya
                        dönüştürüyoruz
                    </p>
                    <div class="relative w-full max-w-sm flex items-center bg-white border border-gray-300 rounded-full shadow-sm p-1 cursor-pointer"
                        onclick="switchView('search'); setTimeout(() => document.getElementById('searchInput').focus(), 100);">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-5 text-black text-lg pointer-events-none"></i>
                        <input type="text" data-i18n-placeholder="search" placeholder="Arama...."
                            class="w-full bg-transparent py-3 pl-12 pr-4 focus:outline-none text-sm pointer-events-none text-black font-poppins font-normal">
                        <button
                            class="bg-[#1C1C1C] text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-[#8C6C47] transition-colors whitespace-nowrap"
                            data-i18n="navSearchBtn">Menü</button>
                    </div>
                </div>

                <div class="w-full max-w-5xl mx-auto px-6 mt-4">
                    <h3 class="text-brand-text font-serif font-bold text-base mb-3 tracking-wide text-left">Kategoriler</h3>
                    <div id="home-category-list" class="grid grid-cols-1 md:grid-cols-4 gap-4 pb-4">
                    </div>
                </div>
            </div>

            <div id="view-search" class="page-view px-6 pt-4 pb-28 md:pb-12">

                <div class="flex flex-col gap-4 max-w-2xl mx-auto mb-6">
                    <div class="flex md:hidden items-center justify-between">
                        <img src="{{ isset($siteSettings['logo']) && $siteSettings['logo'] != '' ? asset($siteSettings['logo']) : asset('images/oztaylan_logo.jpg') }}" class="h-10 w-10 object-contain rounded-full shadow-sm bg-white p-0.5 cursor-pointer" onclick="switchView('home')" alt="Logo">
                        <div class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs font-bold shadow-sm">
                            <span data-i18n="tableLabel">Masa:</span> <span class="current-table-display">-</span>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <button onclick="switchView('home')" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 hover:text-black transition-colors">
                            <i class="fa-solid fa-chevron-left"></i> Geri
                        </button>
                    </div>
                </div>

                @if(isset($siteSettings['wifi_password']) && $siteSettings['wifi_password'] != '')
                    <div class="max-w-2xl mx-auto mb-4 text-center">
                        <div
                            class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full text-sm shadow-sm">
                            <i class="fa-solid fa-wifi text-[#8C6C47]"></i>
                            <span>Wi-Fi: <strong class="text-gray-800">{{ $siteSettings['wifi_password'] }}</strong></span>
                        </div>
                    </div>
                @endif

                <div class="relative mb-6 mt-2 max-w-2xl mx-auto flex items-center bg-white border border-gray-300 rounded-full shadow-sm pr-4">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 transform -translate-y-1/2 text-black text-lg"></i>
                    <input type="text" id="searchInput" oninput="handleSearch(this.value)"
                        data-i18n-placeholder="search" placeholder="Arama...."
                        class="w-full bg-transparent py-3.5 pl-14 pr-12 focus:outline-none text-sm text-black font-poppins font-normal">
                    <i class="fa-solid fa-sliders text-black text-lg cursor-pointer"></i>
                </div>

                <div id="category-list" class="hidden">
                    <p data-i18n="loadingCats" class="text-center text-gray-500 py-4 col-span-full">Kategoriler yükleniyor...</p>
                </div>

                <div id="dynamic-product-list" class="flex flex-col pb-8 max-w-5xl mx-auto">
                    <div class="w-full mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center">
                            <h3 id="dynamic-products-title"
                                class="font-serif text-2xl md:text-3xl font-semibold text-brand-dark"></h3>
                        </div>
                        <div class="flex overflow-x-auto no-scrollbar gap-2 pb-2" id="category-tabs"></div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6" id="products-grid"></div>

                    <div class="mt-8 max-w-5xl mx-auto w-full">
                        <h3 class="font-serif text-2xl md:text-3xl font-semibold text-brand-dark mb-4 text-left">Tavsiye Edilenler</h3>
                        <div class="space-y-4" id="recommended-products-list">
                        </div>
                    </div>
                </div>
            </div>

            <footer class="w-full bg-[#151515] text-gray-500 pt-6 pb-24 md:pb-6 px-6 mt-12 border-t border-white/5 text-center shrink-0">
                <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2 justify-center">
                        <img src="{{ isset($siteSettings['logo']) && $siteSettings['logo'] != '' ? asset($siteSettings['logo']) : asset('images/oztaylan_logo.jpg') }}" class="h-6 w-6 object-contain rounded-full bg-white p-0.5" alt="Logo">
                        <span class="font-bold text-white tracking-wider">{{ $siteSettings['restaurant_name'] ?? '' }}</span>
                    </div>
                    <p>&copy; {{ date('Y') }}. Tüm hakları saklıdır.</p>
                    @if(isset($siteSettings['wifi_password']) && $siteSettings['wifi_password'] != '')
                        <p class="flex items-center gap-1.5 justify-center">
                            <i class="fa-solid fa-wifi text-amber-500"></i> Wi-Fi: <strong class="text-white">{{ $siteSettings['wifi_password'] }}</strong>
                        </p>
                    @endif
                    <p class="text-[10px] uppercase tracking-wider font-semibold">
                        Powered by <a href="#" target="_blank" class="hover:text-amber-500 text-gray-400 transition-colors">Mikale QR Menu</a>
                    </p>
                </div>
            </footer>
        </main>

        <nav
            class="fixed md:hidden bottom-0 left-0 right-0 w-full bg-white rounded-t-3xl shadow-[0_-5px_15px_rgba(0,0,0,0.05)] px-4 py-4 pb-6 flex justify-between items-center text-[10px] sm:text-xs font-medium text-gray-500 z-50">
            <button onclick="switchView('home')"
                class="nav-btn active flex flex-col items-center gap-1 hover:text-brand-gold transition-colors text-brand-gold w-1/3"
                data-target="home">
                <i class="fa-solid fa-house text-lg mb-0.5"></i><span data-i18n="navHome">Ana Sayfa</span>
            </button>
            <button onclick="switchView('search')"
                class="nav-btn flex flex-col items-center gap-1 hover:text-brand-gold transition-colors w-1/3"
                data-target="search">
                <i class="fa-solid fa-magnifying-glass text-lg mb-0.5"></i><span data-i18n="navSearch">Menü</span>
            </button>
            <button onclick="window.open('/admin', '_blank')"
                class="nav-btn flex flex-col items-center gap-1 hover:text-brand-gold transition-colors text-gray-500 w-1/3">
                <i class="fa-solid fa-user-lock text-lg mb-0.5"></i><span>Admin</span>
            </button>
        </nav>

        <div id="overlay" class="overlay" onclick="closeProductModal()"></div>
        <div id="product-modal" class="modal-container shadow-2xl">
            <div class="relative w-full h-56 md:h-64 shrink-0 bg-gray-50">
                <img id="modal-image" src="" class="w-full h-full object-cover" alt="">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                <button type="button" onclick="closeProductModal()"
                    class="absolute top-4 right-4 w-10 h-10 bg-white/90 backdrop-blur rounded-full flex justify-center items-center text-brand-dark shadow-sm hover:scale-105 transition-transform z-10">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="flex-1 flex flex-col bg-white overflow-hidden">
                <div class="p-6 md:p-8 flex-1 overflow-y-auto no-scrollbar">

                    <h2 id="modal-title"
                        class="text-2xl md:text-3xl font-serif font-bold text-brand-dark leading-tight pr-2 mb-4">
                        Yükleniyor...</h2>

                    <div class="flex flex-wrap items-center gap-3 text-xs font-medium text-gray-600 mb-6">
                        <span id="modal-price"
                            class="bg-amber-50 text-brand-gold px-4 py-1.5 rounded-lg border border-amber-100 font-bold text-sm">{{ $siteSettings['currency'] ?? '₺' }}0</span>
                        <span id="modal-cal" class="bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100"><i
                                class="fa-solid fa-fire text-orange-400 mr-1"></i> 0 kcal</span>
                        <span id="modal-time" class="bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100"><i
                                class="fa-regular fa-clock mr-1 text-gray-400"></i> 15 dk</span>
                    </div>

                    <div class="w-full h-px bg-gray-100 mb-6"></div>

                    <p id="modal-desc" class="text-sm text-gray-700 leading-relaxed mb-6">Detaylar yükleniyor...</p>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts

    <script>
        const currencySymbol = "{{ $siteSettings['currency'] ?? '₺' }}";


        const translations = {
            tr: {
                heroDesc: "Gelenekten ilham alan lezzetleri modern bir dokunuşla sunuyor, her ziyareti özel bir anıya dönüştürüyoruz",
                search: "Arama....", tableLabel: "Masa:", loadingCats: "Kategoriler yükleniyor...",
                navHome: "Ana Sayfa", navSearch: "Menü", navSearchBtn: "Menü", searchResults: "Arama Sonuçları"
            },
            en: {
                heroDesc: "Offering tradition-inspired flavors with a modern touch, turning every visit into a special memory",
                search: "Search....", tableLabel: "Table:", loadingCats: "Loading categories...",
                navHome: "Home", navSearch: "Menu", navSearchBtn: "Menu", searchResults: "Search Results"
            }
        };

        let currentLang = 'tr';
        let allProducts = [];
        window.appCategories = [];
        let currentTable = '-';
        let activeCategoryId = null;

        function changeLanguage(lang) {
            currentLang = lang;

            document.querySelectorAll('.btn-lang-tr').forEach(el => {
                if (lang === 'tr') el.classList.add('bg-white', 'text-black', 'shadow-sm', 'font-bold');
                else el.classList.remove('bg-white', 'text-black', 'shadow-sm', 'font-bold');
                if (lang !== 'tr') el.classList.add('text-gray-400', 'bg-transparent');
                else el.classList.remove('text-gray-400', 'bg-transparent');
            });
            document.querySelectorAll('.btn-lang-en').forEach(el => {
                if (lang === 'en') el.classList.add('bg-white', 'text-black', 'shadow-sm', 'font-bold');
                else el.classList.remove('bg-white', 'text-black', 'shadow-sm', 'font-bold');
                if (lang !== 'en') el.classList.add('text-gray-400', 'bg-transparent');
                else el.classList.remove('text-gray-400', 'bg-transparent');
            });

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang][key]) el.innerHTML = translations[lang][key];
            });

            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                const key = el.getAttribute('data-i18n-placeholder');
                if (translations[lang][key]) el.setAttribute('placeholder', translations[lang][key]);
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            const urlParams = new URLSearchParams(window.location.search);
            let tableToken = null;
            if (urlParams.has('masa')) tableToken = urlParams.get('masa');
            else if (urlParams.has('table')) tableToken = urlParams.get('table');

            if (tableToken) {
                fetch(`/api/tables/${tableToken}`)
                    .then(res => res.json())
                    .then(result => {
                        if (result && result.status === 'success') {
                            document.querySelectorAll('.current-table-display').forEach(el => el.innerText = result.data.name);
                        } else {
                            document.querySelectorAll('.current-table-display').forEach(el => el.innerText = tableToken);
                        }
                    })
                    .catch(() => {
                        document.querySelectorAll('.current-table-display').forEach(el => el.innerText = tableToken);
                    });
            } else {
                document.querySelectorAll('.current-table-display').forEach(el => el.innerText = '-');
            }
            changeLanguage('tr');

            const minSplashTime = new Promise(resolve => setTimeout(resolve, 1500));
            const fetchCat = fetch('/api/categories').then(res => res.json()).catch(() => ({ status: 'error', data: [] }));
            const fetchProd = fetchProducts();

            Promise.all([fetchCat, fetchProd, minSplashTime]).then(([catResult]) => {
                hideSplashScreen();
                if (catResult && catResult.status === 'success') {
                    window.appCategories = catResult.data;
                    renderCategories(catResult.data);
                } else {
                    window.appCategories = [
                        { id: 1, name: 'BAŞLANGIÇ', image_url: 'images/baslangic.jpg' },
                        { id: 2, name: 'PİZZA', image_url: 'images/pizza.jpg' },
                        { id: 3, name: 'KEBAP', image_url: 'images/kebap.webp' },
                        { id: 4, name: 'İÇECEKLER', image_url: 'images/kahve.png' }
                    ];
                    renderCategories(window.appCategories);
                }
                if (window.appCategories && window.appCategories.length > 0) {
                    showProducts(window.appCategories[0].id, window.appCategories[0].name);
                }
            }).catch(() => {
                hideSplashScreen();
                window.appCategories = [];
                renderCategories([]);
            });
        });

        function renderCategories(categories) {
            const container = document.getElementById('category-list');
            const homeContainer = document.getElementById('home-category-list');
            container.innerHTML = '';
            if (homeContainer) homeContainer.innerHTML = '';

            categories.forEach(cat => {
                const imgUrl = cat.image_url || '';
                const catName = cat.name.toUpperCase();
                const safeName = catName.replace(/'/g, "\\'");
                
                const itemHtml = `
                    <div class="w-full h-[110px] rounded-[18px] relative overflow-hidden shadow-sm cursor-pointer hover:opacity-95 transition-opacity" onclick="showProducts(${cat.id}, '${safeName}')">
                        <img src="${imgUrl}" class="absolute inset-0 w-full h-full object-cover" alt="${catName}">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/95 via-black/50 to-transparent"></div>
                        <div class="absolute inset-y-0 left-6 flex items-center z-10">
                            <h3 class="text-white font-serif text-[1.1rem] tracking-wide uppercase">${catName}</h3>
                        </div>
                    </div>
                `;
                
                container.innerHTML += itemHtml;
                
                if (homeContainer) {
                    const homeItemHtml = `
                        <div class="w-full h-[110px] rounded-[18px] relative overflow-hidden shadow-sm cursor-pointer hover:opacity-95 transition-opacity" onclick="switchView('search'); showProducts(${cat.id}, '${safeName}')">
                            <img src="${imgUrl}" class="absolute inset-0 w-full h-full object-cover" alt="${catName}">
                            <div class="absolute inset-0 bg-gradient-to-r from-black/95 via-black/50 to-transparent"></div>
                            <div class="absolute inset-y-0 left-6 flex items-center z-10">
                                <h3 class="text-white font-serif text-[1.1rem] tracking-wide uppercase">${catName}</h3>
                            </div>
                        </div>
                    `;
                    homeContainer.innerHTML += homeItemHtml;
                }
            });
        }

        function showProducts(catId, catName) {
            activeCategoryId = catId;
            document.getElementById('searchInput').value = '';
            document.getElementById('category-list').classList.add('hidden');
            document.getElementById('dynamic-product-list').classList.remove('hidden');
            document.getElementById('dynamic-product-list').classList.add('flex');

            let tabsHtml = '';
            window.appCategories.forEach(c => {
                let nameStr = c.name || '';
                let displayTitle = nameStr.charAt(0).toUpperCase() + nameStr.slice(1).toLowerCase();
                let isActive = c.id == catId ? 'bg-[#8C6C47] text-white shadow-sm border-[#8C6C47]' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50';
                let safeName = nameStr.replace(/'/g, "\\'");
                tabsHtml += `<button onclick="showProducts(${c.id}, '${safeName}')" class="px-5 py-2.5 whitespace-nowrap rounded-xl border font-semibold text-sm transition-colors ${isActive}">${displayTitle}</button>`;
            });
            document.getElementById('category-tabs').innerHTML = tabsHtml;

            const filtered = allProducts.filter(p => p.category_id == catId);
            renderProducts(filtered, catName);
        }

        function handleSearch(val) {
            if (!val || val.trim() === '') {
                let cat = window.appCategories.find(c => c.id == activeCategoryId) || window.appCategories[0];
                if (cat) {
                    showProducts(cat.id, cat.name);
                }
                return;
            }

            document.getElementById('category-list').classList.add('hidden');
            document.getElementById('dynamic-product-list').classList.remove('hidden');
            document.getElementById('dynamic-product-list').classList.add('flex');

            const lowerVal = val.toLowerCase();
            const filtered = allProducts.filter(p =>
                p.name.toLowerCase().includes(lowerVal) ||
                (p.description && p.description.toLowerCase().includes(lowerVal))
            );

            const searchTitle = translations[currentLang].searchResults;
            document.getElementById('category-tabs').innerHTML = `<div class="px-5 py-2.5 whitespace-nowrap rounded-xl border border-gray-200 font-semibold text-sm bg-[#8C6C47] text-white shadow-sm">${searchTitle}: "${val}"</div>`;
            renderProducts(filtered, searchTitle);
        }

        function fetchProducts() {
            return fetch('/api/products').then(res => res.json())
                .then(result => { if (result.status === 'success') allProducts = result.data; })
                .catch(err => console.error(err));
        }

        function renderProducts(products, activeCategoryName) {
            const container = document.getElementById('products-grid');
            let nameStr = activeCategoryName || '';
            document.getElementById('dynamic-products-title').innerText = nameStr.charAt(0).toUpperCase() + nameStr.slice(1).toLowerCase();
            let html = '';

            products.forEach(product => {
                const imgUrl = product.image_url || '';
                const safeName = product.name.replace(/'/g, "\\'");
                const kcalText = product.calories ? `${product.calories} kcal` : '250 kcal';
                const timeText = product.prep_time ? `${product.prep_time} min` : '15 min';
                const glutenText = product.is_gluten_free ? ' (Gluten Free)' : '';

                html += `
                    <div onclick="openProductModal(${product.id})" class="bg-white rounded-[1.5rem] overflow-hidden shadow-sm border border-gray-100 cursor-pointer transition-transform duration-300 hover:-translate-y-1 flex flex-col animate-fade-in-up">
                        <div class="relative w-full h-32 bg-gray-50">
                            <img src="${imgUrl}" class="w-full h-full object-cover" alt="">
                        </div>
                        <div class="p-4 flex flex-col justify-between flex-1">
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm line-clamp-1">${product.name}</h4>
                                <div class="flex items-center gap-1.5 text-[9px] text-gray-400 font-semibold mt-1">
                                    <span><i class="fa-solid fa-fire text-orange-400 mr-0.5"></i> ${kcalText}</span>
                                    <span><i class="fa-regular fa-clock mr-0.5 text-gray-400"></i> ${timeText}${glutenText}</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center mt-3 pt-2">
                                <span class="font-extrabold text-gray-900 text-sm">${currencySymbol}${product.price}</span>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            renderRecommendedProducts();
        }

        function renderRecommendedProducts() {
            const container = document.getElementById('recommended-products-list');
            if (!container) return;
            container.innerHTML = '';
            
            const items = allProducts.slice(0, 3);
            let html = '';
            items.forEach(product => {
                const imgUrl = product.image_url || '';
                const safeName = product.name.replace(/'/g, "\\'");
                const kcalText = product.calories ? `${product.calories} kcal` : '300 kcal';
                const timeText = product.prep_time ? `${product.prep_time} min` : '35 min';
                
                html += `
                    <div onclick="openProductModal(${product.id})" class="bg-white rounded-[1.5rem] p-3 shadow-sm border border-gray-100 flex items-center gap-4 cursor-pointer hover:shadow-md transition-shadow">
                        <div class="w-20 h-20 rounded-[1rem] overflow-hidden shrink-0 bg-gray-50">
                            <img src="${imgUrl}" class="w-full h-full object-cover" alt="">
                        </div>
                        <div class="flex-1 min-w-0 pr-2">
                            <h4 class="font-bold text-gray-900 text-base truncate">${product.name}</h4>
                            <p class="text-xs text-gray-500 line-clamp-2 mt-1 leading-snug">${product.description || ''}</p>
                            <div class="flex justify-between items-end mt-3">
                                <span class="font-extrabold text-gray-900 text-base">₺${product.price}</span>
                                <div class="flex items-center gap-2 text-[10px] text-gray-500 font-semibold">
                                    <span><i class="fa-solid fa-fire text-orange-400 mr-1"></i> ${kcalText}</span>
                                    <span><i class="fa-regular fa-clock mr-1 text-gray-400"></i> ${timeText}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        function hideSplashScreen() {
            const splash = document.getElementById('splash-screen');
            splash.classList.add('opacity-0');
            setTimeout(() => splash.remove(), 700);
        }

        function switchView(viewName) {
            document.querySelectorAll('.page-view').forEach(view => view.classList.remove('active'));
            const targetView = document.getElementById(`view-${viewName}`);
            if (targetView) targetView.classList.add('active');

            const header = document.getElementById('main-header');
            if (header) {
                if (viewName === 'home') {
                    header.className = 'hidden';
                } else {
                    header.className = 'hidden md:flex justify-between items-center px-6 pt-12 md:pt-6 pb-4 bg-brand-bg border-b border-gray-100';
                }
            }

            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.remove('text-brand-gold');
                if (btn.dataset.target === viewName) btn.classList.add('text-brand-gold');
            });
        }

        function openProductModal(productId) {
            const product = allProducts.find(p => p.id == productId);
            if (!product) return;

            document.getElementById('modal-image').src = product.image_url || '';
            document.getElementById('modal-title').innerText = product.name;
            document.getElementById('modal-price').innerText = `${currencySymbol}${product.price}`;
            document.getElementById('modal-desc').innerText = product.description;
            document.getElementById('modal-cal').innerHTML = `<i class="fa-solid fa-fire text-orange-400 mr-1"></i> ${product.calories || 0} kcal`;
            document.getElementById('modal-time').innerHTML = `<i class="fa-regular fa-clock mr-1 text-gray-400"></i> ${product.prep_time || 15} dk`;

            document.getElementById('overlay').classList.add('open');
            document.getElementById('product-modal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function backToCategories() {

            document.getElementById('dynamic-product-list').classList.add('hidden');
            document.getElementById('dynamic-product-list').classList.remove('flex');


            document.getElementById('category-list').classList.remove('hidden');


            document.getElementById('searchInput').value = '';
        }
        function closeProductModal() {
            document.getElementById('overlay').classList.remove('open');
            document.getElementById('product-modal').classList.remove('open');
            document.body.style.overflow = '';
        }
    </script>
</body>

</html>