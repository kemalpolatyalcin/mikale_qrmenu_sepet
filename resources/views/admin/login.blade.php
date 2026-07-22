<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikale | Yönetici Girişi</title>
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

<body class="bg-[#F9F8F3] min-h-screen flex flex-col font-poppins">

    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-40 px-6 py-4 flex justify-between items-center border-b border-gray-100 w-full shrink-0">
        <div class="flex items-center gap-3">
            @if(isset($siteSettings['logo']) && $siteSettings['logo'] != '')
                <img src="{{ asset($siteSettings['logo']) }}" class="h-10 object-contain" alt="Logo">
            @else
                <div class="font-allison text-5xl text-black leading-none pt-2">
                    {{ substr($siteSettings['restaurant_name'] ?? 'M', 0, 1) }}</div>
            @endif
            <span class="font-serif font-bold text-lg hidden md:block tracking-widest">{{ $siteSettings['restaurant_name'] ?? '' }}</span>
        </div>

        <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-gray-500">
            <a href="{{ url('/') }}" class="hover:text-[#8C6C47] transition-colors"><span data-i18n="navHome">Ana Sayfa</span></a>
            <a href="{{ url('/') }}" class="hover:text-[#8C6C47] transition-colors"><span data-i18n="navSearch">Menü</span></a>
            <a href="{{ url('/') }}" class="hover:text-[#8C6C47] transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-basket-shopping"></i> <span>Sepet</span>
            </a>
            <a href="{{ url('/admin') }}" class="text-[#8C6C47] font-semibold flex items-center gap-1.5">
                <i class="fa-solid fa-user-lock"></i> <span>Admin</span>
            </a>
        </nav>

        <div class="flex items-center gap-4 text-sm font-semibold">
            <div class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs font-bold shadow-sm">
                <span data-i18n="tableLabel">Masa:</span> <span class="current-table-display">-</span>
            </div>
            <div class="flex items-center bg-gray-50 rounded p-1 border border-gray-200">
                <button
                    class="btn-lang-tr px-2 py-0.5 rounded text-black font-bold bg-white shadow-sm text-xs transition-all"
                    onclick="changeLanguage('tr')">TR</button>
                <button class="btn-lang-en px-2 py-0.5 rounded text-gray-400 font-normal text-xs transition-all"
                    onclick="changeLanguage('en')">EN</button>
            </div>
        </div>
    </header>

    <div class="flex-1 flex items-center justify-center p-4 pb-24 md:pb-4">
        <div class="bg-white p-6 sm:p-10 rounded-2xl sm:rounded-3xl shadow-none sm:shadow-xl w-full max-w-md border-0 sm:border border-gray-100">
            <div class="text-center mb-8">
                <div class="font-allison text-[4.5rem] sm:text-[6rem] text-[#1C1C1C] leading-none mb-2">M</div>
                <h1 data-i18n="title" class="text-xl font-semibold text-gray-800 tracking-wide uppercase">Yönetim Paneli</h1>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-xl text-sm mb-6 text-center border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label data-i18n="emailLabel" class="block text-sm font-medium text-gray-700 mb-1">E-posta Adresi</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#8C6C47] focus:border-[#8C6C47] outline-none transition-all text-sm">
                </div>

                <div>
                    <label data-i18n="passwordLabel" class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#8C6C47] focus:border-[#8C6C47] outline-none transition-all text-sm">
                </div>

                <button type="submit" data-i18n="loginBtn"
                    class="w-full bg-[#1C1C1C] text-white font-medium py-3.5 rounded-xl hover:bg-[#8C6C47] transition-colors mt-4 shadow-md">
                    Giriş Yap
                </button>
            </form>
            <div class="text-center mt-6 pt-4 border-t border-gray-100 text-[10px] text-gray-400 font-semibold tracking-wider">
                <a href="#" target="_blank" class="hover:text-[#8C6C47] transition-colors">MIKALE QR MENU SYSTEM</a> v1.0.0
            </div>
        </div>
    </div>

    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 px-4 py-4 pb-6 flex justify-between items-center text-[10px] sm:text-xs font-medium text-gray-500 z-40 shadow-lg">
        <a href="{{ url('/') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-[#8C6C47] transition-colors w-1/5">
            <i class="fa-solid fa-house text-lg mb-0.5"></i><span data-i18n="navHome">Ana Sayfa</span>
        </a>
        <a href="{{ url('/') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-[#8C6C47] transition-colors w-1/5">
            <i class="fa-solid fa-magnifying-glass text-lg mb-0.5"></i><span data-i18n="navSearch">Menü</span>
        </a>
        <a href="{{ url('/') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-[#8C6C47] transition-colors w-1/5">
            <i class="fa-solid fa-basket-shopping text-lg mb-0.5"></i><span>Sepet</span>
        </a>
        <a href="{{ url('/admin/orders') }}" target="_blank" class="flex flex-col items-center gap-1 text-gray-500 hover:text-[#8C6C47] transition-colors w-1/5">
            <i class="fa-solid fa-desktop text-lg mb-0.5"></i><span>Siparişler</span>
        </a>
        <a href="{{ url('/admin') }}" class="flex flex-col items-center gap-1 text-[#8C6C47] hover:text-[#8C6C47] transition-colors w-1/5 font-semibold">
            <i class="fa-solid fa-user-lock text-lg mb-0.5"></i><span>Admin</span>
        </a>
    </nav>

    <script>
        const translations = {
            tr: {
                tableLabel: "Masa:",
                navHome: "Ana Sayfa",
                navSearch: "Menü",
                title: "Yönetim Paneli",
                subtitle: "Lütfen yönetici bilgilerinizi girin",
                warningTitle: "Bu sayfa sadece adminler içindir!",
                warningSubtitle: "Yetkisiz kişilerin bu panele girişi yasaktır.",
                emailLabel: "E-posta Adresi",
                passwordLabel: "Şifre",
                loginBtn: "Giriş Yap"
            },
            en: {
                tableLabel: "Table:",
                navHome: "Home",
                navSearch: "Menu",
                title: "Admin Panel",
                subtitle: "Please enter your admin credentials",
                warningTitle: "This page is for admins only!",
                warningSubtitle: "Unauthorized access to this panel is prohibited.",
                emailLabel: "Email Address",
                passwordLabel: "Password",
                loginBtn: "Log In"
            }
        };

        function changeLanguage(lang) {
            document.querySelectorAll('.btn-lang-tr').forEach(el => {
                if (lang === 'tr') el.className = 'btn-lang-tr px-2 py-0.5 rounded text-black font-bold bg-white shadow-sm text-xs transition-all';
                else el.className = 'btn-lang-tr px-2 py-0.5 rounded text-gray-400 font-normal text-xs bg-transparent transition-all';
            });
            document.querySelectorAll('.btn-lang-en').forEach(el => {
                if (lang === 'en') el.className = 'btn-lang-en px-2 py-0.5 rounded text-black font-bold bg-white shadow-sm text-xs transition-all';
                else el.className = 'btn-lang-en px-2 py-0.5 rounded text-gray-400 font-normal text-xs bg-transparent transition-all';
            });

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang][key]) el.innerHTML = translations[lang][key];
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
        });
    </script>

</body>

</html>