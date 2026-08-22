<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('goals.index') }}" class="p-2 bg-surface border border-border rounded-lg text-muted hover:text-primary transition-colors" title="Kembali">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-heading font-bold text-heading">Detail Target Keuangan</h2>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 bg-success/10 border border-success/20 text-success rounded-xl flex items-center">
            <i class="ti ti-check w-5 h-5 mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $percentage = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KOLOM KIRI: Ringkasan Target -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-surface rounded-xl border border-border p-6 shadow-sm">
                <h3 class="text-xl font-heading font-bold text-heading mb-2">{{ $goal->name }}</h3>

                @if($goal->description)
                    <p class="text-sm text-muted mb-4 italic">"{{ $goal->description }}"</p>
                @endif

                <div class="mb-5 mt-4">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <p class="text-2xl font-heading font-bold text-heading">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-muted">Dari Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                        </div>
                        <span class="text-lg font-bold {{ $percentage == 100 ? 'text-success' : 'text-primary' }}">{{ $percentage }}%</span>
                    </div>
                    <div class="w-full bg-background border border-border rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full {{ $percentage == 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-border text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted">Tenggat Waktu:</span>
                        <span class="font-medium text-heading">
                            {{ $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->translatedFormat('d M Y') : 'Tanpa Tenggat' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">Status:</span>
                        @if($goal->is_completed)
                            <span class="font-bold text-success">Tercapai 🎉</span>
                        @else
                            <span class="font-medium text-primary">Berjalan</span>
                        @endif
                    </div>
                </div>
            </div>

            @if(!$goal->is_completed)
                <a href="{{ route('savings.create', ['goal_id' => $goal->id]) }}" class="w-full flex items-center justify-center px-4 py-3 bg-primary text-surface rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm">
                    <i class="ti ti-pig-money mr-2 text-xl"></i> Setor Tabungan Baru
                </a>
            @endif
        </div>

        <!-- KOLOM KANAN: Tabel Riwayat Tabungan -->
        <div class="lg:col-span-2">
            <div class="bg-surface rounded-xl border border-border overflow-hidden shadow-sm h-full flex flex-col">
                <div class="p-6 border-b border-border">
                    <h3 class="text-lg font-heading font-bold text-heading flex items-center">
                        <i class="ti ti-history text-primary mr-2 text-xl"></i> Riwayat Setoran Tabungan
                    </h3>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-background/50 border-b border-border">
                            <tr class="text-xs font-medium text-muted uppercase tracking-wider">
                                <th class="px-6 py-3">Tanggal & Catatan</th>
                                <th class="px-6 py-3">Sumber Dana</th>
                                <th class="px-6 py-3 text-right">Nominal</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($savings as $saving)
                                <tr class="hover:bg-background/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-heading">
                                            {{ \Carbon\Carbon::parse($saving->date)->translatedFormat('d M Y') }}
                                        </div>
                                        @if($saving->description)
                                            <div class="text-xs text-muted mt-0.5">{{ $saving->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-heading">{{ $saving->wallet->name ?? 'Dihapus' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-sm font-bold text-success">+ Rp {{ number_format($saving->amount, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('savings.destroy', $saving) }}" method="POST" onsubmit="return confirm('Tarik kembali uang ini ke dompet?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-background border border-border rounded text-muted hover:text-danger transition-colors" title="Batal & Tarik Uang">
                                                <i class="ti ti-arrow-back-up text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-muted text-sm">
                                        Belum ada riwayat tabungan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
