<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikale | Kategoriler</title>
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

    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <header
            class="md:hidden bg-white/90 shadow-sm z-30 px-6 py-4 flex justify-between items-center border-b border-gray-100 shrink-0">
            <div class="font-allison text-3xl text-black leading-none">M</div>
            <button onclick="document.getElementById('mobile-admin-menu').classList.remove('translate-x-full')"
                class="text-gray-800 text-2xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>
        </header>

        <header
            class="hidden md:flex h-20 bg-white/80 backdrop-blur-md items-center justify-between px-8 shadow-sm z-10 shrink-0">
            <h2 class="text-xl font-semibold text-gray-800">Kategori Yönetimi</h2>
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-bold border-2 border-[#8C6C47]">
                    {{ substr(Auth::user()->full_name ?? Auth::user()->name ?? 'A', 0, 1) }}
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 no-scrollbar">

            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-3 border border-green-200">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-0">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b pb-4">Yeni Kategori Ekle</h3>

                        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
                            class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı</label>
                                <input type="text" name="name" required placeholder="Örn: Başlangıçlar"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#8C6C47] focus:border-[#8C6C47] outline-none transition-all text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Görseli</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full px-4 py-2 rounded-xl border border-gray-300 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-[#8C6C47] hover:file:bg-amber-100 transition-all cursor-pointer">
                            </div>

                            <button type="submit"
                                class="w-full bg-[#1C1C1C] text-white font-medium py-3 rounded-xl hover:bg-[#8C6C47] transition-colors mt-4">
                                Kaydet
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="p-4 text-sm font-semibold text-gray-600">Görsel</th>
                                    <th class="p-4 text-sm font-semibold text-gray-600">Kategori Adı</th>
                                    <th class="p-4 text-sm font-semibold text-gray-600 text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($categories as $category)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-4">
                                            <div class="w-16 h-12 rounded-lg bg-gray-200 overflow-hidden shadow-sm">
                                                <img src="{{ asset($category->image_url ?? 'images/placeholder.jpg') }}"
                                                    alt="{{ $category->name }}" class="w-full h-full object-cover">
                                            </div>
                                        </td>
                                        <td class="p-4 font-medium text-gray-800">{{ $category->name }}</td>
                                        <td class="p-4 text-right space-x-2">

                                            <button
                                                onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                                class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </button>

                                            <a href="{{ route('admin.categories.delete', $category->id) }}"
                                                onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-8 text-center text-gray-500">
                                            <i class="fa-solid fa-box-open text-3xl mb-3 text-gray-300"></i>
                                            <p>Henüz hiç kategori eklenmemiş.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="edit-modal"
            class="absolute inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-8 w-full max-w-md mx-4 shadow-2xl relative">

                <button onclick="closeEditModal()"
                    class="absolute top-6 right-6 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <h3 class="text-xl font-semibold text-gray-800 mb-6">Kategoriyi Düzenle</h3>

                <form id="edit-form" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı</label>
                        <input type="text" id="edit-name" name="name" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#8C6C47] focus:border-[#8C6C47] outline-none transition-all text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Görsel Seçin</label>
                        <input type="file" name="image" accept="image/*"
                            class="w-full px-4 py-2 rounded-xl border border-gray-300 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-[#8C6C47] hover:file:bg-amber-100 transition-all cursor-pointer">
                        <p class="text-[11px] text-gray-400 mt-2"><i class="fa-solid fa-circle-info mr-1"></i>Eğer yeni
                            bir görsel seçmezseniz mevcut görsel kullanılmaya devam eder.</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#8C6C47] text-white font-medium py-3.5 rounded-xl hover:bg-[#7a5e3e] transition-colors mt-2 shadow-md">
                        Değişiklikleri Kaydet
                    </button>
                </form>
            </div>
        </div>

    </main>

    <script>
        function openEditModal(id, currentName) {

            const form = document.getElementById('edit-form');
            form.action = `/admin/categories/update/${id}`;

            document.getElementById('edit-name').value = currentName;

            const modal = document.getElementById('edit-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeEditModal() {
            const modal = document.getElementById('edit-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
    @include('admin.partials.mobile-menu')
</body>
</html>