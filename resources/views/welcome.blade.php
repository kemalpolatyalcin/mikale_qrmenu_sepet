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

<body class="bg-[#f0f2f5] m-0 p-4 md:p-10 font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 m-0">Restoran Menüsü</h1>
                <p class="text-gray-600 text-sm md:text-base mt-2">Ürünleri eklediğinde sayfa yenilenmeden sepetin
                    güncellenecek.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <a href="/manager" target="_blank"
                    class="w-full sm:w-auto bg-slate-800 hover:bg-slate-900 text-white px-5 py-3 rounded-xl font-bold transition-colors shadow-md flex justify-center items-center gap-2 text-sm">
                    <i class="fa-solid fa-desktop text-amber-400"></i> <span>Yönetici Paneli</span>
                </a>

                <button onclick="window.dispatchEvent(new CustomEvent('open-cart'))"
                    class="w-full sm:w-auto bg-brand-gold hover:bg-[#735738] text-white px-6 py-3 rounded-xl font-bold transition-colors shadow-md flex justify-center items-center gap-2">
                    <i class="fa-solid fa-basket-shopping"></i> <span>Sepetimi Gör</span>
                </button>
            </div>
        </div>

        <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        </div>
    </div>

    <livewire:cart-drawer />

    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('/api/products')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const grid = document.getElementById('products-grid');
                        grid.innerHTML = '';

                        data.data.forEach(product => {
                            let imgPath = product.image_url || product.image || '';
                            if (imgPath && !imgPath.startsWith('/') && !imgPath.startsWith('http')) {
                                imgPath = '/' + imgPath;
                            }

                            const imageHtml = imgPath
                                ? `<img src="${imgPath}" alt="${product.name}" class="w-full h-48 object-cover rounded-t-lg mb-4">`
                                : `<div class="w-full h-48 bg-gray-100 rounded-t-lg mb-4 flex items-center justify-center text-gray-400"><i class="fa-solid fa-image text-3xl"></i></div>`;

                            const descHtml = product.description
                                ? `<p class="text-gray-500 text-sm mb-3 line-clamp-2">${product.description}</p>`
                                : '';

                            grid.innerHTML += `
                                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition-transform hover:-translate-y-1 h-full">
                                    <div>
                                        ${imageHtml}
                                        <h3 class="text-lg font-semibold text-gray-800 m-0 mb-1">${product.name}</h3>
                                        ${descHtml}
                                        <p class="text-xl font-bold text-red-500 m-0 mb-4">${product.price} TL</p>
                                    </div>
                                    <button onclick="Livewire.dispatch('add-to-cart', { id: ${product.id}, name: '${product.name.replace(/'/g, "\\'")}', price: ${product.price} })" class="w-full py-3 mt-auto bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold transition-colors">
                                        Sepete Ekle
                                    </button>
                                </div>
                            `;
                        });
                    }
                })
                .catch(error => console.error(error));
        });
    </script>
</body>

</html>