<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-heading font-bold text-heading">Target Keuangan</h2>
            <a href="{{ route('goals.create') }}" class="bg-primary text-surface px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 flex items-center transition-colors shadow-sm">
                <i class="ti ti-plus mr-2 text-lg"></i> Buat Target Baru
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 bg-success/10 border border-success/20 text-success rounded-xl flex items-center">
            <i class="ti ti-check w-5 h-5 mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($goals as $goal)
            @php
                // Hitung persentase (Maksimal 100%)
                $percentage = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0;

                // Cek apakah deadline sudah terlewat
                $isDeadlinePassed = false;
                if ($goal->deadline && !$goal->is_completed) {
                    $isDeadlinePassed = \Carbon\Carbon::parse($goal->deadline)->isPast();
                }
            @endphp

            <!-- Tambahkan h-full agar card membentang penuh di grid -->
            <div class="bg-surface rounded-xl border border-border p-6 shadow-sm flex flex-col h-full relative transition-all hover:shadow-md">

                <!-- BAGIAN ATAS: Header, Progress, & Info (Gunakan flex-1 agar mengisi ruang kosong) -->
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-heading font-bold text-heading flex items-center">
                                <a href="{{ route('goals.show', $goal) }}" class="hover:text-primary hover:underline transition-colors">
                                    {{ $goal->name }}
                                </a>
                            </h3>
                            <!-- Status Badge -->
                            <div class="mt-1 flex gap-2 flex-wrap">
                                @if($goal->is_completed)
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-success/10 text-success rounded border border-success/20">Tercapai</span>
                                @elseif($isDeadlinePassed)
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-danger/10 text-danger rounded border border-danger/20">Terlewat</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-primary/10 text-primary rounded border border-primary/20">Berjalan</span>
                                @endif
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-background border border-border flex items-center justify-center flex-shrink-0 text-muted">
                            <i class="ti ti-target text-xl"></i>
                        </div>
                    </div>

                    <!-- Progress Bar Area -->
                    <div class="mb-5 mt-2">
                        <div class="flex justify-between items-end mb-2">
                            <div>
                                <p class="text-2xl font-heading font-bold text-heading">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-muted">Terkumpul dari Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                            </div>
                            <span class="text-lg font-bold {{ $percentage == 100 ? 'text-success' : 'text-primary' }}">{{ $percentage }}%</span>
                        </div>

                        <div class="w-full bg-background border border-border rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full transition-all duration-1000 ease-out {{ $percentage == 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Info Tambahan (Deskripsi & Link) -->
                    <div class="mb-4">
                        @if($goal->description)
                            <p class="text-xs text-muted italic line-clamp-2 mb-2" title="{{ $goal->description }}">"{{ $goal->description }}"</p>
                        @endif

                        @if($goal->purchase_link)
                            <a href="{{ $goal->purchase_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-xs font-medium text-primary hover:underline bg-primary/5 px-2 py-1 rounded">
                                <i class="ti ti-external-link mr-1"></i> Buka Link Referensi
                            </a>
                        @endif
                    </div>
                </div>

                <!-- BAGIAN BAWAH: Tombol & Footer (Selalu terdorong ke bawah) -->
                <div class="mt-auto">
                    <!-- Tombol Action Setor Tabungan -->
                    @if(!$goal->is_completed)
                        <div class="mb-4">
                            <a href="{{ route('savings.create', ['goal_id' => $goal->id]) }}" class="w-full flex items-center justify-center px-4 py-2 bg-primary/10 text-primary border border-primary/20 rounded-lg text-sm font-medium hover:bg-primary hover:text-surface transition-colors">
                                <i class="ti ti-pig-money mr-2 text-lg"></i> Setor Tabungan
                            </a>
                        </div>
                    @endif

                    <!-- Footer Card -->
                    <div class="pt-4 border-t border-border flex items-center justify-between">
                        <div class="flex items-center text-xs text-muted font-medium">
                            <i class="ti ti-calendar mr-1.5 text-sm {{ $isDeadlinePassed ? 'text-danger' : '' }}"></i>
                            <span class="{{ $isDeadlinePassed ? 'text-danger' : '' }}">
                                @if($goal->deadline)
                                    Tenggat: {{ \Carbon\Carbon::parse($goal->deadline)->translatedFormat('d M Y') }}
                                @else
                                    Tanpa Tenggat
                                @endif
                            </span>
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('goals.edit', $goal) }}" class="p-1.5 text-muted hover:text-primary transition-colors" title="Edit">
                                <i class="ti ti-edit text-lg"></i>
                            </a>
                            <form action="{{ route('goals.destroy', $goal) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus target ini? (Catatan Tabungan di dalamnya mungkin ikut terhapus atau kehilangan relasi).');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-muted hover:text-danger transition-colors" title="Hapus">
                                    <i class="ti ti-trash text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface border border-dashed border-border rounded-xl p-8 flex flex-col items-center justify-center text-center">
                <i class="ti ti-target text-4xl text-muted mb-3"></i>
                <h3 class="text-lg font-heading font-bold text-heading mb-1">Belum ada Target Keuangan</h3>
                <p class="text-muted text-sm mb-4">Mulai wujudkan impian Anda dengan membuat target menabung (misal: Beli Motor, Dana Darurat).</p>
                <a href="{{ route('goals.create') }}" class="bg-primary text-surface px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-primary/90 shadow-sm">
                    Buat Target Pertama
                </a>
            </div>
        @endforelse
    </div>
</x-app-layout>
