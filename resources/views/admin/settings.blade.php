@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto pb-10">
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Restoran Profili</h1>
            <p class="text-sm text-gray-500">Logonuzu, fotoğraflarınızı ve müşterilerinize görünen diğer işletme
                bilgilerinizi buradan güncelleyebilirsiniz.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="delete_logo" id="delete-logo-input" value="0">
            <input type="hidden" name="delete_cover_image" id="delete-cover-input" value="0">

            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2"><i
                        class="fa-solid fa-image text-[#8C6C47] mr-2"></i>Görsel Ayarları</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Restoran Logosu</label>
                        <div id="logo-preview-container" class="mb-3 bg-gray-50 p-2 rounded-xl border border-gray-200 {{ (isset($settings['logo']) && $settings['logo'] != '') ? 'inline-block' : 'hidden' }} relative">
                            <img id="logo-preview-img" src="{{ (isset($settings['logo']) && $settings['logo'] != '') ? asset($settings['logo']) : '' }}" alt="Logo" class="h-16 object-contain">
                            <button type="button" id="delete-logo-btn" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-md">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                        <input type="file" id="logo-input" name="logo" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-[#8C6C47] hover:file:bg-amber-100 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Menü Karşılama Fotoğrafı (Arka Plan)</label>
                        <div id="cover-preview-container" class="mb-3 bg-gray-50 rounded-xl overflow-hidden border border-gray-200 h-24 relative {{ (isset($settings['cover_image']) && $settings['cover_image'] != '') ? 'block' : 'hidden' }}">
                            <img id="cover-preview-img" src="{{ (isset($settings['cover_image']) && $settings['cover_image'] != '') ? asset($settings['cover_image']) : '' }}" alt="Cover" class="w-full h-full object-cover">
                            <button type="button" id="delete-cover-btn" class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-md">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                        <input type="file" id="cover-input" name="cover_image" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-[#8C6C47] hover:file:bg-amber-100 transition-all">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2"><i
                        class="fa-solid fa-store text-[#8C6C47] mr-2"></i>Temel Bilgiler</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Restoran Adı</label>
                        <input type="text" id="restaurant-name-input" name="restaurant_name" value="{{ $settings['restaurant_name'] ?? 'Mikale' }}"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#8C6C47] transition-all">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Slogan (Karşılama Metni)</label>
                        <input type="text" name="slogan"
                            value="{{ $settings['slogan'] ?? 'Harika Tatlar, Güzel Anılar...' }}"
                            placeholder="Örn: En taze kahveler, en güzel tatlılar..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#8C6C47] transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Para Birimi Sembolü</label>
                        <select name="currency"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#8C6C47] transition-all">
                            <option value="₺" {{ ($settings['currency'] ?? '') == '₺' ? 'selected' : '' }}>₺ (TL)</option>
                            <option value="$" {{ ($settings['currency'] ?? '') == '$' ? 'selected' : '' }}>$ (USD)</option>
                            <option value="€" {{ ($settings['currency'] ?? '') == '€' ? 'selected' : '' }}>€ (EUR)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Müşteri Wi-Fi Şifresi</label>
                        <input type="text" name="wifi_password" value="{{ $settings['wifi_password'] ?? '' }}"
                            placeholder="Menüde gösterilecek..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#8C6C47] transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Telefon Numarası</label>
                        <input type="text" name="phone" value="{{ $settings['phone'] ?? '' }}"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#8C6C47] transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Çalışma Saatleri</label>
                        <input type="text" name="working_hours" value="{{ $settings['working_hours'] ?? '' }}"
                            placeholder="Örn: 09:00 - 23:00"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#8C6C47] transition-all">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Açık Adres</label>
                        <textarea name="address" rows="2"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#8C6C47] transition-all">{{ $settings['address'] ?? '' }}</textarea>
                    </div>
                </div>

                <div class="mt-8 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="bg-[#1C1C1C] hover:bg-[#8C6C47] text-white font-medium py-3 px-8 rounded-xl transition-colors shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Değişiklikleri Kaydet
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="toast" class="fixed top-6 right-6 z-50 transform translate-y-[-20px] opacity-0 pointer-events-none transition-all duration-300 bg-white shadow-xl border border-gray-100 rounded-2xl p-4 flex items-center gap-3 max-w-sm">
        <div id="toast-icon-container" class="w-8 h-8 rounded-full flex items-center justify-center">
            <i id="toast-icon" class="fa-solid text-sm"></i>
        </div>
        <div>
            <p id="toast-message" class="text-sm font-semibold text-gray-800"></p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const nameInput = document.getElementById("restaurant-name-input");
            const logoInput = document.getElementById("logo-input");
            const coverInput = document.getElementById("cover-input");
            const form = document.getElementById("settings-form");
            const submitBtn = form.querySelector("button[type='submit']");

            const deleteLogoBtn = document.getElementById("delete-logo-btn");
            const deleteCoverBtn = document.getElementById("delete-cover-btn");
            const deleteLogoInput = document.getElementById("delete-logo-input");
            const deleteCoverInput = document.getElementById("delete-cover-input");

            function showToast(message, isSuccess = true) {
                const toast = document.getElementById('toast');
                const iconContainer = document.getElementById('toast-icon-container');
                const icon = document.getElementById('toast-icon');
                const msg = document.getElementById('toast-message');

                msg.innerText = message;
                if (isSuccess) {
                    iconContainer.className = 'w-8 h-8 rounded-full flex items-center justify-center bg-green-50 text-green-500';
                    icon.className = 'fa-solid fa-circle-check text-sm';
                } else {
                    iconContainer.className = 'w-8 h-8 rounded-full flex items-center justify-center bg-red-50 text-red-500';
                    icon.className = 'fa-solid fa-circle-exclamation text-sm';
                }

                toast.classList.remove('opacity-0', 'translate-y-[-20px]', 'pointer-events-none');
                toast.classList.add('opacity-100', 'translate-y-0');

                setTimeout(() => {
                    toast.classList.remove('opacity-100', 'translate-y-0');
                    toast.classList.add('opacity-0', 'translate-y-[-20px]', 'pointer-events-none');
                }, 3000);
            }

            nameInput.addEventListener("input", function () {
                const newName = nameInput.value.trim() || "MIKALE";
                document.querySelectorAll(".sidebar-restaurant-name").forEach(el => {
                    el.textContent = newName;
                });
                document.querySelectorAll(".sidebar-restaurant-letter").forEach(el => {
                    el.textContent = newName.charAt(0).toUpperCase();
                });
            });

            logoInput.addEventListener("change", function () {
                const file = logoInput.files[0];
                if (file) {
                    deleteLogoInput.value = "0";
                    const url = URL.createObjectURL(file);
                    const img = document.getElementById("logo-preview-img");
                    const container = document.getElementById("logo-preview-container");
                    img.src = url;
                    container.classList.remove("hidden");
                    container.classList.add("inline-block");
                }
            });

            coverInput.addEventListener("change", function () {
                const file = coverInput.files[0];
                if (file) {
                    deleteCoverInput.value = "0";
                    const url = URL.createObjectURL(file);
                    const img = document.getElementById("cover-preview-img");
                    const container = document.getElementById("cover-preview-container");
                    img.src = url;
                    container.classList.remove("hidden");
                    container.classList.add("block");
                }
            });

            deleteLogoBtn.addEventListener("click", function () {
                deleteLogoInput.value = "1";
                logoInput.value = "";
                const container = document.getElementById("logo-preview-container");
                container.classList.add("hidden");
                container.classList.remove("inline-block");
                document.getElementById("logo-preview-img").src = "";
            });

            deleteCoverBtn.addEventListener("click", function () {
                deleteCoverInput.value = "1";
                coverInput.value = "";
                const container = document.getElementById("cover-preview-container");
                container.classList.add("hidden");
                container.classList.remove("block");
                document.getElementById("cover-preview-img").src = "";
            });

            form.addEventListener("submit", function (e) {
                e.preventDefault();
                submitBtn.disabled = true;
                const originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Kaydediliyor...';

                fetch(form.action, {
                    method: "POST",
                    body: new FormData(form),
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => {
                    if (res.ok || res.status === 302 || res.redirected) {
                        showToast("Ayarlar başarıyla kaydedildi!");
                    } else {
                        throw new Error();
                    }
                })
                .catch(() => {
                    showToast("Bir hata oluştu!", false);
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                });
            });
        });
    </script>
@endsection