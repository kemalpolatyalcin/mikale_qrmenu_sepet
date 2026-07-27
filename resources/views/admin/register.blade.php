@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Kasa</h1>
            <p class="text-sm text-gray-500">Kasa durumunu ve geçmiş işlem kayıtlarını inceleyin.</p>
        </div>

        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-end justify-between gap-4">
            <form action="{{ route('admin.register') }}" method="GET" class="flex flex-wrap items-end gap-3 flex-1">
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray-500 mb-1.5 uppercase">Başlangıç Tarihi</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#8C6C47] transition-all">
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray-500 mb-1.5 uppercase">Bitiş Tarihi</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#8C6C47] transition-all">
                </div>
                <button type="submit"
                    class="bg-[#8C6C47] hover:bg-[#735738] text-white font-bold py-2 px-6 rounded-xl text-sm transition-colors shadow-md">
                    Filtrele
                </button>
                @if($startDate || $endDate)
                    <a href="{{ route('admin.register') }}"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-6 rounded-xl text-sm transition-colors text-center">
                        Temizle
                    </a>
                @endif
            </form>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <span class="text-xs text-gray-400 font-bold uppercase block mb-1">Nakit</span>
                <span class="text-lg font-extrabold text-emerald-600">₺{{ number_format($totals['Nakit'], 2) }}</span>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <span class="text-xs text-gray-400 font-bold uppercase block mb-1">Kredi Kartı</span>
                <span class="text-lg font-extrabold text-blue-600">₺{{ number_format($totals['Kredi Kartı'], 2) }}</span>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <span class="text-xs text-gray-400 font-bold uppercase block mb-1">Cari Hesap</span>
                <span class="text-lg font-extrabold text-amber-600">₺{{ number_format($totals['Cari Hesap'], 2) }}</span>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <span class="text-xs text-gray-400 font-bold uppercase block mb-1">Yemek Fişi</span>
                <span class="text-lg font-extrabold text-purple-600">₺{{ number_format($totals['Yemek Fişi'], 2) }}</span>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <span class="text-xs text-gray-400 font-bold uppercase block mb-1">Online</span>
                <span class="text-lg font-extrabold text-indigo-600">₺{{ number_format($totals['Online Ödeme'], 2) }}</span>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <span class="text-xs text-gray-400 font-bold uppercase block mb-1">Ödenmez</span>
                <span class="text-lg font-extrabold text-rose-500">₺{{ number_format($totals['Ödenmez'], 2) }}</span>
            </div>
            <div class="bg-slate-900 rounded-xl p-4 shadow-sm border border-slate-800 text-white col-span-2 lg:col-span-1">
                <span class="text-xs text-slate-400 font-bold uppercase block mb-1">Toplam Ciro</span>
                <span class="text-lg font-extrabold text-amber-400">₺{{ number_format($totals['grand_total'], 2) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">İşlem Geçmişi</h3>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">Tarih</th>
                            <th class="px-6 py-3.5">Masa</th>
                            <th class="px-6 py-3.5">Tutar</th>
                            <th class="px-6 py-3.5">Ödeme Tipi</th>
                            <th class="px-6 py-3.5">Detaylar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($transactions as $t)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                    {{ $t->created_at->format('d.m.Y H:i:s') }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $t->table_name }}</td>
                                <td class="px-6 py-4 font-bold text-amber-600">₺{{ number_format($t->amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold
                                                {{ $t->payment_method === 'Nakit' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}
                                                {{ $t->payment_method === 'Kredi Kartı' ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}
                                                {{ $t->payment_method === 'Cari Hesap' ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                                                {{ $t->payment_method === 'Yemek Fişi' ? 'bg-purple-50 text-purple-700 border border-purple-100' : '' }}
                                                {{ $t->payment_method === 'Online Ödeme' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : '' }}
                                                {{ $t->payment_method === 'Ödenmez' ? 'bg-rose-50 text-rose-700 border border-rose-100' : '' }}
                                            ">
                                        {{ $t->payment_method }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" title="{{ $t->details }}">
                                    {{ $t->details }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-cash-register text-4xl mb-3 opacity-30 block"></i>
                                    Henüz tahsilat işlemi gerçekleştirilmemiş.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection