<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikale | Sanctum Giriş Testi</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="bg-[#F9F8F3] min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800 uppercase tracking-wide">Sanctum Test</h1>
        </div>

        <form id="loginForm" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                <input type="email" id="email" required autofocus
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#8C6C47] focus:border-[#8C6C47] outline-none transition-all text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                <input type="password" id="password" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#8C6C47] focus:border-[#8C6C47] outline-none transition-all text-sm">
            </div>

            <button type="submit"
                class="w-full bg-[#1C1C1C] text-white font-medium py-3 rounded-xl hover:bg-[#8C6C47] transition-colors shadow-md">
                Giriş Yap (Generate Token)
            </button>
        </form>

        <div id="message" class="mt-4 text-center text-sm font-medium text-red-600"></div>

        <div class="mt-8 pt-6 border-t border-gray-100 space-y-4">
            <button id="fetchDataBtn"
                class="w-full bg-emerald-600 text-white font-medium py-3 rounded-xl hover:bg-emerald-700 transition-colors shadow-md">
                Korumalı API Verisini Çek
            </button>

            <button id="logoutBtn"
                class="w-full bg-rose-600 text-white font-medium py-3 rounded-xl hover:bg-rose-700 transition-colors shadow-md hidden">
                Çıkış Yap (Revoke Token)
            </button>
        </div>

        <pre id="apiResult" class="mt-4 p-3 bg-gray-50 rounded-xl text-xs overflow-auto max-h-40 hidden"></pre>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const messageDiv = document.getElementById('message');
            const logoutBtn = document.getElementById('logoutBtn');
            const apiResult = document.getElementById('apiResult');
            
            messageDiv.innerText = '';
            apiResult.classList.add('hidden');

            try {
                const response = await fetch('/api/admin/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    localStorage.setItem('admin_token', data.token);
                    messageDiv.className = 'mt-4 text-center text-sm font-medium text-emerald-600';
                    messageDiv.innerText = 'Giriş başarılı! Token kaydedildi.';
                    logoutBtn.classList.remove('hidden');
                } else {
                    messageDiv.className = 'mt-4 text-center text-sm font-medium text-rose-600';
                    messageDiv.innerText = data.message || 'Giriş başarısız.';
                }
            } catch (error) {
                messageDiv.className = 'mt-4 text-center text-sm font-medium text-rose-600';
                messageDiv.innerText = 'Bağlantı hatası oluştu.';
            }
        });

        document.getElementById('fetchDataBtn').addEventListener('click', async function() {
            const token = localStorage.getItem('admin_token');
            const messageDiv = document.getElementById('message');
            const apiResult = document.getElementById('apiResult');

            messageDiv.innerText = '';
            apiResult.classList.add('hidden');

            if (!token) {
                messageDiv.className = 'mt-4 text-center text-sm font-medium text-rose-600';
                messageDiv.innerText = 'Token bulunamadı! Lütfen önce giriş yapın.';
                return;
            }

            try {
                const response = await fetch('/api/admin/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.status === 401) {
                    messageDiv.className = 'mt-4 text-center text-sm font-medium text-rose-600';
                    messageDiv.innerText = 'Yetkisiz erişim! Token geçersiz veya süresi dolmuş.';
                    localStorage.removeItem('admin_token');
                    document.getElementById('logoutBtn').classList.add('hidden');
                    return;
                }

                messageDiv.className = 'mt-4 text-center text-sm font-medium text-emerald-600';
                messageDiv.innerText = 'Token doğrulandı! API testi başarılı.';
            } catch (error) {
                messageDiv.className = 'mt-4 text-center text-sm font-medium text-rose-600';
                messageDiv.innerText = 'API bağlantı hatası.';
            }
        });

        document.getElementById('logoutBtn').addEventListener('click', async function() {
            const token = localStorage.getItem('admin_token');
            const messageDiv = document.getElementById('message');
            const logoutBtn = document.getElementById('logoutBtn');

            messageDiv.innerText = '';

            if (!token) {
                return;
            }

            try {
                await fetch('/api/admin/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {}

            localStorage.removeItem('admin_token');
            logoutBtn.classList.add('hidden');
            messageDiv.className = 'mt-4 text-center text-sm font-medium text-emerald-600';
            messageDiv.innerText = 'Güvenli çıkış yapıldı. Token silindi.';
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('admin_token')) {
                document.getElementById('logoutBtn').classList.remove('hidden');
            }
        });
    </script>

</body>
</html>
