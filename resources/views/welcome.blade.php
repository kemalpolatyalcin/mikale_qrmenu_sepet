<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikale Menü</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    @livewireStyles
</head>

<body class="bg-[#f0f2f5] m-0 p-4 md:p-10 font-sans" x-data>

    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 m-0">Restoran Menüsü</h1>
                <p class="text-gray-600 text-sm md:text-base mt-2">Ürünleri eklediğinde sayfa yenilenmeden sepetin
                    güncellenecek.</p>
            </div>

            <button @click="$dispatch('open-cart')"
                class="w-full sm:w-auto bg-brand-gold hover:bg-[#735738] text-white px-6 py-3 rounded-xl font-bold transition-colors shadow-md flex justify-center items-center gap-2">
                <i class="fa-solid fa-basket-shopping"></i> <span>Sepetimi Gör</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">

            <div
                class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition-transform hover:-translate-y-1">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 m-0 mb-1">Adana Kebap</h3>
                    <p class="text-xl font-bold text-red-500 m-0 mb-4">280 TL</p>
                </div>
                <button onclick="Livewire.dispatch('add-to-cart', { id: 1, name: 'Adana Kebap', price: 280 })"
                    class="w-full py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold transition-colors">
                    Sepete Ekle
                </button>
            </div>

            <div
                class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition-transform hover:-translate-y-1">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 m-0 mb-1">Lahmacun</h3>
                    <p class="text-xl font-bold text-red-500 m-0 mb-4">80 TL</p>
                </div>
                <button onclick="Livewire.dispatch('add-to-cart', { id: 2, name: 'Lahmacun', price: 80 })"
                    class="w-full py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold transition-colors">
                    Sepete Ekle
                </button>
            </div>

            <div
                class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition-transform hover:-translate-y-1">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 m-0 mb-1">Şalgam Suyu</h3>
                    <p class="text-xl font-bold text-red-500 m-0 mb-4">35 TL</p>
                </div>
                <button onclick="Livewire.dispatch('add-to-cart', { id: 3, name: 'Şalgam Suyu', price: 35 })"
                    class="w-full py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold transition-colors">
                    Sepete Ekle
                </button>
            </div>

        </div>
    </div>

    <livewire:cart-drawer />

    @livewireScripts
</body>

</html>