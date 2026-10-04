@extends('layouts.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div class="d-flex align-items-center me-3">
                <h1 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">Migrasi Saldo Santri ke SIM Baru</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-200 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Pengaturan</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-200 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-dark">Migrasi Saldo</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">

            @if (session('success'))
            <div class="alert alert-success d-flex align-items-center p-5 mb-6 rounded border border-success">
                <i class="fas fa-check-circle fs-2hx text-success me-4"></i>
                <div class="d-flex flex-column">
                    <h5 class="mb-1 text-success fw-bolder">Berhasil!</h5>
                    <span class="fs-7 text-gray-800">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center p-5 mb-6 rounded border border-danger">
                <i class="fas fa-exclamation-triangle fs-2hx text-danger me-4"></i>
                <div class="d-flex flex-column">
                    <h5 class="mb-1 text-danger fw-bolder">Pemberitahuan:</h5>
                    <span class="fs-7 text-gray-800">{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <!--begin::Card Utama-->
            <div class="card card-flush shadow-sm mb-8">
                <!--begin::Card Header-->
                <div class="card-header pt-6 border-0">
                    <div class="card-title">
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-45px">
                                <span class="symbol-label bg-light-primary">
                                    <i class="fas fa-exchange-alt fs-2 text-primary"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bolder text-gray-800 mb-1">Manajemen Migrasi Saldo & Tutup Buku Per Kelas</h3>
                                <span class="text-muted fs-7">Proses pemindahan saldo santri secara terverifikasi ke Aplikasi Baru (SIM Cahaya Tasbih).</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card Header-->

                <div class="card-body pt-2">

                    <!--begin::Section Kunci Login-->
                    <form action="{{ route('migration-saldo.save-settings') }}" method="POST" id="form-migration-settings">
                        @csrf
                        <div class="mb-8 bg-light-danger p-6 rounded-3 border border-danger border-dashed">
                            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-3">
                                        <span class="symbol-label bg-danger text-white">
                                            <i class="fas fa-user-lock fs-3 text-white"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h4 class="text-gray-900 fw-bolder mb-0">Kunci Akses Login Sistem (Cut-Off Transaksi Saldo)</h4>
                                        <span class="text-muted fs-7">Bekukan transaksi agar saldo santri tidak bergerak selama proses migrasi.</span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-danger px-4 py-2">
                                    <i class="fas fa-save me-1"></i> Simpan Pengaturan Kunci
                                </button>
                            </div>

                            <!--begin::Toggle Switch-->
                            <div class="d-flex align-items-center justify-content-between p-4 mb-4 bg-white rounded border">
                                <div>
                                    <label class="fs-6 fw-bold text-gray-800" for="is_login_locked">
                                        Status Kunci Login Sistem
                                    </label>
                                    <div class="text-muted fs-8">Jika aktif, kasir kantin/koperasi, piket, dan wali santri otomatis tidak bisa login / belanja.</div>
                                </div>
                                <div class="form-check form-switch form-check-custom form-check-danger form-check-solid">
                                    <input class="form-check-input h-30px w-50px cursor-pointer" type="checkbox" name="is_login_locked" id="is_login_locked" value="1"
                                        {{ !empty($applicationSetting?->is_login_locked) ? 'checked' : '' }} />
                                </div>
                            </div>
                            <!--end::Toggle Switch-->

                            <!--begin::Allowed Roles-->
                            <div class="mb-4">
                                <label class="fs-7 fw-bold form-label mb-2 text-gray-700">
                                    <i class="fas fa-shield-alt text-primary me-1"></i>
                                    Role yang TETAP Boleh Login Saat Sistem Dikunci:
                                </label>
                                @php
                                    $allowedRoles = $applicationSetting?->getAllowedRoles() ?? ['SUPER ADMIN', 'Bendahara SMP', 'BENDAHARA MA'];
                                @endphp
                                <div class="row g-2 bg-white p-3 rounded border">
                                    @foreach ($roles as $role)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check form-check-custom form-check-sm">
                                            <input class="form-check-input" type="checkbox" name="allowed_roles_when_locked[]"
                                                value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                {{ in_array($role->name, $allowedRoles) ? 'checked' : '' }} />
                                            <label class="form-check-label text-gray-800 fw-bold fs-8 ms-2" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                                @if (str_contains(strtoupper($role->name), 'BENDAHARA') || str_contains(strtoupper($role->name), 'SUPER ADMIN'))
                                                    <span class="badge badge-light-success fs-9 py-0 px-1 ms-1">Disarankan</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <!--end::Allowed Roles-->

                            <!--begin::Pesan Notifikasi & Server Params-->
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="fs-7 fw-bold form-label text-gray-700" for="login_locked_message">
                                        <i class="fas fa-comment-alt text-warning me-1"></i> Pesan Saat Login Ditolak:
                                    </label>
                                    <textarea class="form-control form-control-solid fs-7" id="login_locked_message" name="login_locked_message" rows="2"
                                        placeholder="Pesan penolakan login...">{{ $applicationSetting?->login_locked_message ?? 'Mohon maaf, sistem aplikasi lama sedang ditutup sementara untuk proses migrasi data ke aplikasi baru. Silakan hubungi Bendahara.' }}</textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-7 fw-bold form-label text-gray-700">URL Server SIM Baru:</label>
                                    <input type="text" class="form-control form-control-solid fs-7" name="new_app_url" id="input_new_app_url"
                                        value="{{ $applicationSetting?->new_app_url ?? 'https://sim.cahayatasbih.or.id' }}" />
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-7 fw-bold form-label text-gray-700">Secret Token Migrasi:</label>
                                    <input type="text" class="form-control form-control-solid fs-7" name="migration_token" id="input_migration_token"
                                        value="{{ $applicationSetting?->migration_token ?? 'cahaya-tasbih-migration-secret' }}" />
                                </div>
                            </div>
                            <!--end::Pesan Notifikasi & Server Params-->
                        </div>
                    </form>
                    <!--end::Section Kunci Login-->

                    <!--begin::Banner Informasi Tutup Buku-->
                    <div class="alert alert-warning d-flex align-items-center p-5 mb-6 rounded-3 border border-warning bg-white shadow-xs">
                        <i class="fas fa-exclamation-triangle fs-2hx text-warning me-4"></i>
                        <div class="d-flex flex-column">
                            <h5 class="mb-1 text-warning fw-bolder">Ketentuan Penting Tutup Buku & Keamanan Riwayat Saldo:</h5>
                            <div class="fs-7 text-gray-800">
                                <ul class="mb-0 ps-4">
                                    <li><strong>Saldo Menjadi Rp 0:</strong> Setiap kelas yang berhasil dikirim ke Aplikasi Baru akan otomatis mengalami <strong>Tutup Buku</strong> (saldo santri di aplikasi lama berubah menjadi <strong>Rp 0</strong>).</li>
                                    <li><strong>Riwayat Mutasi Tetap Utuh:</strong> Seluruh riwayat transaksi masa lalu <strong>TIDAK DIHAPUS</strong>. Nilai saldo terakhir tersimpan abadi di kolom <strong>Arsip Saldo</strong> dan dapat dilihat detailnya pada tombol riwayat mutasi.</li>
                                    <li><strong>Pengiriman Sekelas demi Sekelas:</strong> Dilakukan bertahap per kelas agar beban server ringan dan mudah diverifikasi oleh Bendahara.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--end::Banner Informasi Tutup Buku-->

                    <!--begin::Stat Ringkasan-->
                    <div class="row g-4 mb-6">
                        <div class="col-md-3 col-sm-6">
                            <div class="bg-light-primary p-4 rounded-3 border border-primary border-dashed text-center">
                                <div class="text-primary fs-7 fw-bold">TOTAL SANTRI AKTIF</div>
                                <div class="fs-2x fw-bolder text-gray-900 mt-1">{{ number_format($totalActiveStudents ?? 0, 0, ',', '.') }}</div>
                                <div class="text-muted fs-8">Santri Status ACTIVE</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="bg-light-success p-4 rounded-3 border border-success border-dashed text-center">
                                <div class="text-success fs-7 fw-bold">TOTAL SALDO ACUAN</div>
                                <div class="fs-2x fw-bolder text-success mt-1">Rp {{ number_format($totalActiveSaldo ?? 0, 0, ',', '.') }}</div>
                                <div class="text-muted fs-8">Master Saldo Keseluruhan</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="bg-light-info p-4 rounded-3 border border-info border-dashed text-center">
                                <div class="text-info fs-7 fw-bold">JUMLAH KELAS / ROMBEL</div>
                                <div class="fs-2x fw-bolder text-gray-900 mt-1">{{ count($classrooms ?? []) }}</div>
                                <div class="text-muted fs-8">Kelas Santri Aktif</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="bg-light-secondary p-4 rounded-3 border text-center">
                                <div class="text-muted fs-7 fw-bold">TERAKHIR DIKIRIM</div>
                                <div class="fs-5 fw-bolder text-gray-800 mt-2 font-mono">
                                    {{ !empty($applicationSetting?->last_migration_sent_at) ? \Carbon\Carbon::parse($applicationSetting->last_migration_sent_at)->translatedFormat('d M Y H:i') : 'Belum Pernah' }}
                                </div>
                                <div class="text-muted fs-8">Riwayat Kirim Migrasi</div>
                            </div>
                        </div>
                    </div>
                    <!--end::Stat Ringkasan-->

                    <!--begin::Tabel Daftar Kelas untuk Migrasi & Tutup Buku-->
                    <div class="card card-flush bg-white border mb-6">
                        <div class="card-header pt-4 pb-2">
                            <div class="card-title">
                                <h4 class="fw-bolder text-gray-800 mb-0">
                                    <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                                    Daftar Kelas untuk Migrasi & Tutup Buku
                                </h4>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-light-primary fs-8">Klik baris kelas untuk membuka Rincian Santri</span>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 fs-7" id="table-classrooms-migration">
                                    <thead>
                                        <tr class="fw-bolder text-muted bg-light">
                                            <th class="ps-4 min-w-40px">NO</th>
                                            <th class="min-w-140px">KELAS / ROMBEL</th>
                                            <th class="min-w-140px">SEKOLAH / UPT</th>
                                            <th class="min-w-100px text-center">SANTRI AKTIF</th>
                                            <th class="min-w-140px text-end">TOTAL SALDO</th>
                                            <th class="min-w-130px text-center">STATUS BUKU</th>
                                            <th class="min-w-180px text-center pe-4">AKSI MIGRASI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($classrooms as $index => $c)
                                        @php
                                            $cSaldo = (int) ($c->total_saldo ?? 0);
                                            $isClosed = in_array((string)$c->id, $migratedClassroomIds ?? []);
                                        @endphp
                                        <tr id="row-class-{{ $c->id }}" class="classroom-main-row" data-class-id="{{ $c->id }}">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-icon btn-sm btn-light-primary w-22px h-22px rounded-circle btn-toggle-panel"
                                                        data-class-id="{{ $c->id }}" data-class-name="{{ $c->name }}" title="Buka/Tutup Rincian Santri">
                                                        <i class="fas fa-plus fs-9" id="icon-toggle-{{ $c->id }}"></i>
                                                    </button>
                                                    <span class="fw-bold font-mono">{{ $index + 1 }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bolder text-gray-800 fs-6">{{ $c->name }}</span>
                                            </td>
                                            <td>{{ $c->school?->name ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-light-primary fw-bold font-mono">{{ number_format($c->students_count) }} Santri</span>
                                            </td>
                                            <td class="text-end fw-bolder font-mono {{ $cSaldo < 0 ? 'text-danger' : ($cSaldo > 0 ? 'text-success' : 'text-muted') }}">
                                                Rp {{ number_format($cSaldo, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($isClosed)
                                                    <span class="badge badge-light-success fw-bold py-1 px-2">
                                                        <i class="fas fa-check-circle text-success me-1"></i> Sudah Tutup Buku
                                                    </span>
                                                @elseif ($cSaldo > 0)
                                                    <span class="badge badge-light-warning fw-bold py-1 px-2">
                                                        <i class="fas fa-clock text-warning me-1"></i> Belum Tutup Buku
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-info fw-bold py-1 px-2">
                                                        <i class="fas fa-info-circle text-info me-1"></i> Saldo Rp 0 (Belum Dimigrasi)
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center pe-4">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn btn-sm {{ $isClosed ? 'btn-light-secondary' : ($cSaldo > 0 ? 'btn-primary' : 'btn-light-primary') }} py-1 px-3 btn-migrate-class"
                                                        data-class-id="{{ $c->id }}"
                                                        data-class-name="{{ $c->name }}"
                                                        data-students="{{ $c->students_count }}"
                                                        data-saldo="{{ number_format($cSaldo, 0, ',', '.') }}"
                                                        data-is-closed="{{ $isClosed ? '1' : '0' }}">
                                                        <i class="fas fa-paper-plane me-1"></i> {{ $isClosed ? 'Kirim Ulang' : ($cSaldo > 0 ? 'Kirim & Tutup Buku' : 'Kirim Snapshot (Rp 0)') }}
                                                    </button>

                                                    @if ($isClosed)
                                                    <button type="button" class="btn btn-sm btn-light-danger py-1 px-3 btn-reverse-class"
                                                        data-class-id="{{ $c->id }}"
                                                        data-class-name="{{ $c->name }}"
                                                        data-students="{{ $c->students_count }}"
                                                        title="Tarik balik saldo real-time dari SIM Baru dan buka kembali kelas">
                                                        <i class="fas fa-undo-alt me-1"></i> Tarik Balik
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!--begin::Nested Table Expandable Panel-->
                                        <tr class="nested-expandable-row d-none" id="panel-class-{{ $c->id }}">
                                            <td colspan="7" class="p-0 border-0 bg-transparent">
                                                <div class="nested-panel-wrapper p-4 my-2 mx-3 rounded bg-light-primary border border-primary border-dashed shadow-sm">
                                                    <!-- Header bar matching user reference -->
                                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-gray-300 flex-wrap gap-2">
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <span class="bullet bg-primary w-8px h-8px rounded-circle"></span>
                                                            <span class="fs-6 fw-bolder text-gray-800">Rincian Santri & Saldo Kelas {{ $c->name }} ({{ number_format($c->students_count) }} Santri)</span>
                                                            <span class="badge badge-light-primary fw-bold px-2 py-1 fs-9">{{ $c->school?->name ?? 'Madrasah' }}</span>
                                                            @if ($isClosed)
                                                                <span class="badge badge-light-success fw-bold py-1 px-2 fs-9"><i class="fas fa-check-circle text-success me-1"></i> Sudah Tutup Buku</span>
                                                            @elseif ($cSaldo > 0)
                                                                <span class="badge badge-light-warning fw-bold py-1 px-2 fs-9"><i class="fas fa-clock text-warning me-1"></i> Belum Tutup Buku</span>
                                                            @else
                                                                <span class="badge badge-light-info fw-bold py-1 px-2 fs-9"><i class="fas fa-info-circle text-info me-1"></i> Saldo Rp 0</span>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="fs-8 fw-bold text-gray-500 text-uppercase tracking-wider">Total Saldo Aktif:</span>
                                                            <span class="fs-6 fw-bolder text-success font-mono">Rp {{ number_format($cSaldo, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- Search Bar inside Panel -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                                                        <div class="position-relative w-300px">
                                                            <input type="text" class="form-control form-control-sm form-control-solid ps-9 fs-7 input-search-inner"
                                                                data-target="inner-tbody-{{ $c->id }}" placeholder="Cari nama santri / NIS di kelas ini..." />
                                                            <span class="position-absolute top-50 translate-middle-y ms-3 text-gray-400">
                                                                <i class="fas fa-search fs-8"></i>
                                                            </span>
                                                        </div>
                                                        <div class="text-muted fs-8">
                                                            <i class="fas fa-info-circle text-primary me-1"></i> Menampilkan rincian santri dengan arsip saldo sebelum tutup buku & saldo saat ini
                                                        </div>
                                                    </div>

                                                    <!-- Inner Table Container: 2 KOLOM SALDO (ARSIP SALDO & SALDO SEKARANG) -->
                                                    <div class="bg-white rounded border shadow-xs table-responsive" style="max-height: 380px; overflow-y: auto;">
                                                        <table class="table table-sm table-row-dashed align-middle mb-0 gs-3 gy-2 fs-7">
                                                            <thead class="bg-light sticky-top">
                                                                <tr class="text-gray-500 fw-bold fs-8 text-uppercase tracking-wider border-bottom">
                                                                    <th class="ps-3" style="width: 5%">#</th>
                                                                    <th style="width: 15%">NIS</th>
                                                                    <th style="width: 32%">NAMA SANTRI</th>
                                                                    <th class="text-end text-primary" style="width: 16%">ARSIP SALDO</th>
                                                                    <th class="text-end text-success" style="width: 16%">SALDO SEKARANG</th>
                                                                    <th class="text-center pe-3" style="width: 16%">STATUS BUKU</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="inner-tbody-{{ $c->id }}" data-loaded="0">
                                                                <tr>
                                                                    <td colspan="6" class="text-center py-4 text-muted">
                                                                        <i class="fas fa-spinner fa-spin me-2 text-primary fs-5"></i> Memuat data santri kelas {{ $c->name }}...
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Footer of Expandable Panel -->
                                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-gray-200">
                                                        <button type="button" class="btn btn-sm btn-light btn-close-panel" data-class-id="{{ $c->id }}">
                                                            <i class="fas fa-chevron-up me-1"></i> Sembunyikan Rincian
                                                        </button>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-sm {{ $isClosed ? 'btn-light-secondary' : ($cSaldo > 0 ? 'btn-primary' : 'btn-light-primary') }} py-1 px-3 btn-migrate-class"
                                                                data-class-id="{{ $c->id }}"
                                                                data-class-name="{{ $c->name }}"
                                                                data-students="{{ $c->students_count }}"
                                                                data-saldo="{{ number_format($cSaldo, 0, ',', '.') }}"
                                                                data-is-closed="{{ $isClosed ? '1' : '0' }}">
                                                                <i class="fas fa-paper-plane me-1"></i> {{ $isClosed ? 'Kirim Ulang' : ($cSaldo > 0 ? 'Kirim & Tutup Buku' : 'Kirim Snapshot (Rp 0)') }}
                                                            </button>
                                                            @if ($isClosed)
                                                            <button type="button" class="btn btn-sm btn-light-danger py-1 px-3 btn-reverse-class"
                                                                data-class-id="{{ $c->id }}"
                                                                data-class-name="{{ $c->name }}"
                                                                data-students="{{ $c->students_count }}">
                                                                <i class="fas fa-undo-alt me-1"></i> Tarik Balik dari SIM Baru
                                                            </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <!--end::Nested Table Expandable Panel-->

                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                Tidak ada data kelas santri aktif yang ditemukan.
                                            </td>
                                        </tr>
                                        @endforelse

                                        @if (!empty($unassignedCount) && $unassignedCount > 0)
                                        @php
                                            $uSaldo = (int) $unassignedSaldo;
                                            $uClosed = in_array('unassigned', $migratedClassroomIds ?? []);
                                        @endphp
                                        <tr id="row-class-unassigned" class="classroom-main-row bg-light-warning" data-class-id="unassigned">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-icon btn-sm btn-light-warning w-22px h-22px rounded-circle btn-toggle-panel"
                                                        data-class-id="unassigned" data-class-name="Tanpa Kelas" title="Buka/Tutup Rincian Santri">
                                                        <i class="fas fa-plus fs-9" id="icon-toggle-unassigned"></i>
                                                    </button>
                                                    <span class="fw-bold font-mono">#</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bolder text-gray-800 fs-6">Tanpa Kelas (Belum Di-assign)</span>
                                            </td>
                                            <td><span class="text-muted">-</span></td>
                                            <td class="text-center">
                                                <span class="badge badge-light-danger fw-bold font-mono">{{ number_format($unassignedCount) }} Santri</span>
                                            </td>
                                            <td class="text-end fw-bolder font-mono {{ $uSaldo < 0 ? 'text-danger' : ($uSaldo > 0 ? 'text-success' : 'text-muted') }}">
                                                Rp {{ number_format($uSaldo, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($uClosed)
                                                    <span class="badge badge-light-success fw-bold py-1 px-2">
                                                        <i class="fas fa-check-circle text-success me-1"></i> Sudah Tutup Buku
                                                    </span>
                                                @elseif ($uSaldo > 0)
                                                    <span class="badge badge-light-warning fw-bold py-1 px-2">
                                                        <i class="fas fa-clock text-warning me-1"></i> Belum Tutup Buku
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-info fw-bold py-1 px-2">
                                                        <i class="fas fa-info-circle text-info me-1"></i> Saldo Rp 0 (Belum Dimigrasi)
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center pe-4">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn btn-sm {{ $uClosed ? 'btn-light-secondary' : ($uSaldo > 0 ? 'btn-primary' : 'btn-light-primary') }} py-1 px-3 btn-migrate-class"
                                                        data-class-id="unassigned"
                                                        data-class-name="Tanpa Kelas"
                                                        data-students="{{ $unassignedCount }}"
                                                        data-saldo="{{ number_format($uSaldo, 0, ',', '.') }}"
                                                        data-is-closed="{{ $uClosed ? '1' : '0' }}">
                                                        <i class="fas fa-paper-plane me-1"></i> {{ $uClosed ? 'Kirim Ulang' : ($uSaldo > 0 ? 'Kirim & Tutup Buku' : 'Kirim Snapshot (Rp 0)') }}
                                                    </button>
                                                    @if ($uClosed)
                                                    <button type="button" class="btn btn-sm btn-light-danger py-1 px-3 btn-reverse-class"
                                                        data-class-id="unassigned"
                                                        data-class-name="Tanpa Kelas"
                                                        data-students="{{ $unassignedCount }}">
                                                        <i class="fas fa-undo-alt me-1"></i> Tarik Balik
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!--begin::Nested Table Expandable Panel (Tanpa Kelas)-->
                                        <tr class="nested-expandable-row d-none" id="panel-class-unassigned">
                                            <td colspan="7" class="p-0 border-0 bg-transparent">
                                                <div class="nested-panel-wrapper p-4 my-2 mx-3 rounded bg-light-warning border border-warning border-dashed shadow-sm">
                                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-gray-300">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <span class="bullet bg-warning w-10px h-10px rounded-circle"></span>
                                                            <span class="fs-6 fw-bolder text-gray-800">Rincian Santri Tanpa Kelas ({{ number_format($unassignedCount) }} Santri)</span>
                                                            <span class="badge badge-light-danger fw-bold px-3 py-1 fs-8">Belum Di-assign</span>
                                                            @if ($uClosed)
                                                                <span class="badge badge-light-success fw-bold py-1 px-2 fs-8"><i class="fas fa-check-circle text-success me-1"></i> Sudah Tutup Buku</span>
                                                            @elseif ($uSaldo > 0)
                                                                <span class="badge badge-light-warning fw-bold py-1 px-2 fs-8"><i class="fas fa-clock text-warning me-1"></i> Belum Tutup Buku</span>
                                                            @else
                                                                <span class="badge badge-light-info fw-bold py-1 px-2 fs-8"><i class="fas fa-info-circle text-info me-1"></i> Saldo Rp 0</span>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="fs-7 fw-bold text-gray-500 text-uppercase tracking-wider">Total Saldo:</span>
                                                            <span class="fs-6 fw-bolder text-success font-mono">Rp {{ number_format($uSaldo, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                                                        <div class="position-relative w-300px">
                                                            <input type="text" class="form-control form-control-sm form-control-solid ps-9 fs-7 input-search-inner"
                                                                data-target="inner-tbody-unassigned" placeholder="Cari nama santri / NIS..." />
                                                            <span class="position-absolute top-50 translate-middle-y ms-3 text-gray-400">
                                                                <i class="fas fa-search fs-8"></i>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="bg-white rounded border shadow-xs table-responsive" style="max-height: 380px; overflow-y: auto;">
                                                        <table class="table table-sm table-row-dashed align-middle mb-0 gs-3 gy-2 fs-7">
                                                            <thead class="bg-light sticky-top">
                                                                <tr class="text-gray-500 fw-bold fs-8 text-uppercase tracking-wider border-bottom">
                                                                    <th class="ps-3" style="width: 5%">#</th>
                                                                    <th style="width: 15%">NIS</th>
                                                                    <th style="width: 32%">NAMA SANTRI</th>
                                                                    <th class="text-end text-primary" style="width: 16%">ARSIP SALDO</th>
                                                                    <th class="text-end text-success" style="width: 16%">SALDO SEKARANG</th>
                                                                    <th class="text-center pe-3" style="width: 16%">STATUS BUKU</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="inner-tbody-unassigned" data-loaded="0">
                                                                <tr>
                                                                    <td colspan="6" class="text-center py-4 text-muted">
                                                                        <i class="fas fa-spinner fa-spin me-2 text-warning fs-5"></i> Memuat data santri tanpa kelas...
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-gray-200">
                                                        <button type="button" class="btn btn-sm btn-light btn-close-panel" data-class-id="unassigned">
                                                            <i class="fas fa-chevron-up me-1"></i> Sembunyikan Rincian
                                                        </button>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-sm {{ $uClosed ? 'btn-light-secondary' : ($uSaldo > 0 ? 'btn-primary' : 'btn-light-primary') }} py-1 px-3 btn-migrate-class"
                                                                data-class-id="unassigned"
                                                                data-class-name="Tanpa Kelas"
                                                                data-students="{{ $unassignedCount }}"
                                                                data-saldo="{{ number_format($uSaldo, 0, ',', '.') }}"
                                                                data-is-closed="{{ $uClosed ? '1' : '0' }}">
                                                                <i class="fas fa-paper-plane me-1"></i> {{ $uClosed ? 'Kirim Ulang' : ($uSaldo > 0 ? 'Kirim & Tutup Buku' : 'Kirim Snapshot (Rp 0)') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <!--end::Nested Table Expandable Panel (Tanpa Kelas)-->
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::Tabel Daftar Kelas-->

                    <!--begin::Pencarian Global Santri (Collapsible)-->
                    <div class="card card-flush bg-white border mb-6">
                        <div class="card-header pt-3 pb-3 d-flex justify-content-between align-items-center cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapse-global-search">
                            <div class="card-title">
                                <h5 class="fw-bolder text-gray-700 mb-0">
                                    <i class="fas fa-search-plus text-primary me-2"></i>
                                    Pencarian Global Seluruh Santri (Opsional)
                                </h5>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light-primary fs-8 fw-bold">Klik untuk Membuka / Menutup</span>
                            </div>
                        </div>
                        <div id="collapse-global-search" class="collapse">
                            <div class="card-body pt-3">
                                <div class="d-flex justify-content-end mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="fs-7 fw-bold text-gray-700 me-1">Filter Kelas:</label>
                                        <select id="filter_classroom_id" class="form-select form-select-sm form-select-solid w-200px">
                                            <option value="">Semua Kelas ({{ number_format($totalActiveStudents ?? 0, 0, ',', '.') }} Santri)</option>
                                            @foreach ($classrooms as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->students_count }} santri)</option>
                                            @endforeach
                                            @if (!empty($unassignedCount) && $unassignedCount > 0)
                                                <option value="unassigned">Tanpa Kelas ({{ $unassignedCount }} santri)</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 fs-7" id="table-migration-preview">
                                        <thead>
                                            <tr class="fw-bolder text-muted bg-light">
                                                <th class="ps-4 min-w-40px">NO</th>
                                                <th class="min-w-100px">NIS</th>
                                                <th class="min-w-180px">NAMA SANTRI</th>
                                                <th class="min-w-90px">KELAS</th>
                                                <th class="min-w-140px text-end text-success">SALDO UTAMA</th>
                                                <th class="min-w-120px text-end pe-4">TABUNGAN</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Pencarian Global Santri-->

                </div>
            </div>
            <!--end::Card Utama-->

        </div>
    </div>
</div>

<!-- Hidden form for sending migration per class -->
<form action="{{ route('migration-saldo.send') }}" method="POST" id="form-send-migration" class="d-none">
    @csrf
    <input type="hidden" name="classroom_id" id="hidden_classroom_id" />
    <input type="hidden" name="new_app_url" id="hidden_new_app_url" />
    <input type="hidden" name="migration_token" id="hidden_migration_token" />
</form>

<!-- Hidden form for failback / reverse migration per class -->
<form action="{{ route('migration-saldo.reverse') }}" method="POST" id="form-reverse-migration" class="d-none">
    @csrf
    <input type="hidden" name="classroom_id" id="reverse_hidden_classroom_id" />
    <input type="hidden" name="new_app_url" id="reverse_hidden_new_app_url" />
    <input type="hidden" name="migration_token" id="reverse_hidden_migration_token" />
</form>

<!--begin::Modal Riwayat Mutasi Santri-->
<div class="modal fade" id="modal-student-mutations" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3">
            <div class="modal-header pb-0 border-0 justify-content-between">
                <div>
                    <h3 class="fw-bolder text-gray-800 mb-1">
                        <i class="fas fa-receipt text-primary me-2"></i> Riwayat & Arsip Saldo Santri
                    </h3>
                    <span class="text-muted fs-7">Rincian mutasi transaksi dan snapshot saldo penutupan buku.</span>
                </div>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body py-4">
                <!-- Info Santri Card -->
                <div class="bg-light-primary p-4 rounded-3 border border-primary border-dashed mb-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted fs-8 font-bold">NAMA SANTRI</div>
                            <div class="fs-6 fw-bolder text-gray-900" id="mutasi-modal-name">-</div>
                            <div class="text-muted fs-8 font-mono">NIS: <span id="mutasi-modal-nis">-</span> | Kelas: <span id="mutasi-modal-class">-</span></div>
                        </div>
                        <div class="col-sm-3 text-sm-end">
                            <div class="text-muted fs-8 font-bold">ARSIP SALDO TERAKHIR</div>
                            <div class="fs-5 fw-bolder text-primary font-mono" id="mutasi-modal-archive">-</div>
                            <div class="text-muted fs-9" id="mutasi-modal-closed-at">-</div>
                        </div>
                        <div class="col-sm-3 text-sm-end">
                            <div class="text-muted fs-8 font-bold">SALDO SEKARANG</div>
                            <div class="fs-5 fw-bolder text-gray-800 font-mono" id="mutasi-modal-current">-</div>
                            <div class="badge badge-light-success fs-9 py-0 px-2 mt-1">Status Buku Aktif</div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Mutasi -->
                <div class="table-responsive rounded border" style="max-height: 320px; overflow-y: auto;">
                    <table class="table table-sm table-row-dashed align-middle mb-0 gs-3 gy-2 fs-7">
                        <thead class="bg-light sticky-top">
                            <tr class="text-gray-500 fw-bold fs-8 text-uppercase border-bottom">
                                <th class="ps-3" style="width: 20%">WAKTU</th>
                                <th style="width: 12%">TIPE</th>
                                <th class="text-end" style="width: 18%">JUMLAH</th>
                                <th class="text-end" style="width: 18%">SALDO SBLM</th>
                                <th class="text-end" style="width: 18%">SALDO SSDH</th>
                                <th class="pe-3" style="width: 14%">KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody id="mutasi-modal-tbody">
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Memuat riwayat mutasi...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer pt-0 border-0 justify-content-between">
                <a href="{{ route('report-saldo.index') }}" target="_blank" class="btn btn-sm btn-light-primary">
                    <i class="fas fa-external-link-alt me-1"></i> Buka Menu Laporan Saldo Lengkap
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Riwayat Mutasi Santri-->

<style>
    .nested-panel-wrapper {
        background-color: #f8faff !important;
        border: 1.5px solid #b5d0ff !important;
        box-shadow: 0 4px 15px rgba(0, 50, 150, 0.05);
    }
    .classroom-main-row {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .classroom-main-row:hover {
        background-color: #f0f6ff !important;
    }
    .classroom-main-row.is-open {
        background-color: #eaf3ff !important;
        border-left: 4px solid #009ef7 !important;
    }
    .btn-toggle-panel {
        transition: all 0.2s ease;
    }
</style>

@push('js')
<script>
    $(document).ready(function() {
        // DataTable Global Search
        var table = $('#table-migration-preview').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('migration-saldo.student-data') }}",
                data: function(d) {
                    d.classroom_id = $('#filter_classroom_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'ps-4' },
                { data: 'nis', name: 'nis', className: 'font-mono' },
                { data: 'name', name: 'name', className: 'fw-bold text-gray-800' },
                { data: 'classroom_name', name: 'classroom.name' },
                { data: 'saldo', name: 'saldo', className: 'text-end font-mono' },
                { data: 'saving', name: 'saving', className: 'text-end pe-4 font-mono' }
            ],
            language: {
                search: "Cari Santri / NIS:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ santri",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Sebelum"
                }
            }
        });

        $('#filter_classroom_id').on('change', function() {
            table.ajax.reload();
        });

        // Toggle Nested Expandable Panel
        function toggleClassroomPanel(classId) {
            var panelRow = $('#panel-class-' + classId);
            var mainRow = $('#row-class-' + classId);
            var toggleIcon = $('#icon-toggle-' + classId);
            var btnToggle = $('.btn-toggle-panel[data-class-id="' + classId + '"]');

            if (panelRow.hasClass('d-none')) {
                panelRow.removeClass('d-none');
                mainRow.addClass('is-open');
                toggleIcon.removeClass('fa-plus').addClass('fa-minus');
                btnToggle.addClass('btn-primary text-white').removeClass('btn-light-primary btn-light-warning');

                var tbody = $('#inner-tbody-' + classId);
                if (tbody.data('loaded') != '1') {
                    $.ajax({
                        url: "{{ route('migration-saldo.classroom-students') }}",
                        type: "GET",
                        data: { classroom_id: classId },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === 'success') {
                                var html = '';
                                if (res.students && res.students.length > 0) {
                                    res.students.forEach(function(s, idx) {
                                        // Kolom ARSIP SALDO (Kiri)
                                        var archiveHtml = '';
                                        if (s.archive_saldo !== null && s.archive_saldo !== undefined) {
                                            archiveHtml = '<span class="font-mono fw-bolder text-primary">Rp ' + Number(s.archive_saldo).toLocaleString('id-ID') + '</span>';
                                            if (s.closed_at) {
                                                archiveHtml += '<div class="fs-9 text-muted font-mono" title="Waktu Tutup Buku"><i class="fas fa-history text-primary me-1"></i>' + s.closed_at + '</div>';
                                            }
                                        } else {
                                            archiveHtml = '<span class="text-muted fs-8">-</span>';
                                        }

                                        // Kolom SALDO SEKARANG (Kanan)
                                        var currentClass = s.current_saldo > 0 ? 'text-success fw-bolder' : (s.current_saldo < 0 ? 'text-danger fw-bolder' : 'text-muted fw-bold');
                                        var currentHtml = '<span class="font-mono ' + currentClass + '">Rp ' + Number(s.current_saldo).toLocaleString('id-ID') + '</span>';

                                        // Status badge
                                        var statusBadge = s.is_closed 
                                            ? '<span class="badge badge-light-success fs-9 py-1 px-2"><i class="fas fa-check-circle text-success me-1"></i> Sudah Tutup Buku</span>'
                                            : (s.current_saldo > 0 
                                                ? '<span class="badge badge-light-warning fs-9 py-1 px-2"><i class="fas fa-clock text-warning me-1"></i> Belum Tutup Buku</span>'
                                                : '<span class="badge badge-light-info fs-9 py-1 px-2"><i class="fas fa-info-circle text-info me-1"></i> Saldo Rp 0</span>');

                                        var btnMutasi = '<button type="button" class="btn btn-icon btn-xs btn-light-primary rounded-circle ms-2 btn-show-mutation" data-student-id="' + s.id + '" title="Lihat Riwayat Mutasi"><i class="fas fa-receipt fs-9"></i></button>';

                                        html += '<tr class="student-item-row hover-bg-light" data-search="' + (s.name + ' ' + s.nis).toLowerCase() + '">';
                                        html += '  <td class="ps-3 text-gray-500 font-mono">' + (idx + 1) + '</td>';
                                        html += '  <td class="font-mono text-gray-700 fw-bold">' + (s.nis || '-') + '</td>';
                                        html += '  <td><span class="fw-bold text-gray-800">' + s.name + '</span></td>';
                                        html += '  <td class="text-end">' + archiveHtml + '</td>';
                                        html += '  <td class="text-end">' + currentHtml + '</td>';
                                        html += '  <td class="text-center pe-3"><div class="d-flex align-items-center justify-content-center">' + statusBadge + btnMutasi + '</div></td>';
                                        html += '</tr>';
                                    });
                                } else {
                                    html = '<tr><td colspan="6" class="text-center py-4 text-muted fst-italic">Tidak ada santri aktif di kelas ini.</td></tr>';
                                }
                                tbody.html(html);
                                tbody.data('loaded', '1');
                            }
                        },
                        error: function() {
                            tbody.html('<tr><td colspan="6" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Gagal memuat data santri. Silakan coba lagi.</td></tr>');
                        }
                    });
                }
            } else {
                panelRow.addClass('d-none');
                mainRow.removeClass('is-open');
                toggleIcon.removeClass('fa-minus').addClass('fa-plus');
                btnToggle.removeClass('btn-primary text-white').addClass('btn-light-primary');
            }
        }

        // Click row reveals panel
        $(document).on('click', '.classroom-main-row', function(e) {
            if ($(e.target).closest('button, a, input, select').length) {
                return;
            }
            var classId = $(this).data('class-id');
            toggleClassroomPanel(classId);
        });

        // Click (+) button
        $(document).on('click', '.btn-toggle-panel', function(e) {
            e.stopPropagation();
            var classId = $(this).data('class-id');
            toggleClassroomPanel(classId);
        });

        // Hide panel button
        $(document).on('click', '.btn-close-panel', function() {
            var classId = $(this).data('class-id');
            toggleClassroomPanel(classId);
        });

        // Search inside panel
        $(document).on('keyup', '.input-search-inner', function() {
            var val = $(this).val().toLowerCase();
            var targetTbodyId = $(this).data('target');
            $('#' + targetTbodyId + ' tr.student-item-row').each(function() {
                var text = $(this).data('search') || '';
                if (text.indexOf(val) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Modal Riwayat Mutasi Santri
        $(document).on('click', '.btn-show-mutation', function(e) {
            e.stopPropagation();
            var studentId = $(this).data('student-id');
            $('#modal-student-mutations').modal('show');
            $('#mutasi-modal-tbody').html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2 text-primary fs-5"></i> Mengambil riwayat mutasi...</td></tr>');

            $.ajax({
                url: "{{ route('migration-saldo.student-mutations') }}",
                type: "GET",
                data: { student_id: studentId },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        var st = res.student;
                        $('#mutasi-modal-name').text(st.name);
                        $('#mutasi-modal-nis').text(st.nis);
                        $('#mutasi-modal-class').text(st.classroom);
                        $('#mutasi-modal-current').text('Rp ' + Number(st.current_saldo).toLocaleString('id-ID'));
                        
                        if (st.archive_saldo !== null) {
                            $('#mutasi-modal-archive').text('Rp ' + Number(st.archive_saldo).toLocaleString('id-ID'));
                            $('#mutasi-modal-closed-at').text(st.closed_at ? 'Tutup: ' + st.closed_at : 'Tutup Buku');
                        } else {
                            $('#mutasi-modal-archive').text('-');
                            $('#mutasi-modal-closed-at').text('Belum Tutup Buku');
                        }

                        var html = '';
                        if (res.mutations && res.mutations.length > 0) {
                            res.mutations.forEach(function(m) {
                                var isClosing = m.description && m.description.indexOf('Penutupan Buku') > -1;
                                var rowClass = isClosing ? 'bg-light-warning fw-bold' : '';
                                var typeBadge = m.type === 'IN' 
                                    ? '<span class="badge badge-light-success fs-9 py-0 px-1">MASUK</span>' 
                                    : '<span class="badge badge-light-danger fs-9 py-0 px-1">KELUAR</span>';

                                html += '<tr class="' + rowClass + '">';
                                html += '  <td class="ps-3 font-mono fs-8">' + m.date + '</td>';
                                html += '  <td>' + typeBadge + '</td>';
                                html += '  <td class="text-end font-mono">Rp ' + Number(m.amount).toLocaleString('id-ID') + '</td>';
                                html += '  <td class="text-end font-mono text-gray-500">Rp ' + Number(m.balance_before).toLocaleString('id-ID') + '</td>';
                                html += '  <td class="text-end font-mono fw-bold text-gray-800">Rp ' + Number(m.balance_after).toLocaleString('id-ID') + '</td>';
                                html += '  <td class="pe-3 fs-8" title="' + m.description + '">' + (isClosing ? '<strong class="text-danger"><i class="fas fa-flag me-1"></i>Tutup Buku</strong>' : m.description) + '</td>';
                                html += '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="6" class="text-center py-4 text-muted fst-italic">Belum ada riwayat transaksi.</td></tr>';
                        }
                        $('#mutasi-modal-tbody').html(html);
                    }
                },
                error: function() {
                    $('#mutasi-modal-tbody').html('<tr><td colspan="6" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Gagal memuat data mutasi.</td></tr>');
                }
            });
        });

        // Migrate Action
        $(document).on('click', '.btn-migrate-class', function(e) {
            e.stopPropagation();
            var classId = $(this).data('class-id');
            var className = $(this).data('class-name');
            var students = $(this).data('students');
            var saldo = $(this).data('saldo');
            var isClosed = $(this).data('is-closed') == '1';

            var title = isClosed ? 'Kirim Ulang Data Kelas ' + className + '?' : 'Kirim & Tutup Buku Kelas ' + className + '?';
            var htmlMsg = isClosed 
                ? 'Kelas ini sudah pernah ditutup buku sebelumnya.<br><br>Apakah Anda ingin mengirim ulang snapshot data ke SIM Baru?'
                : 'Data saldo santri di kelas <strong>' + className + '</strong> (' + students + ' santri) dengan total saldo <strong>Rp ' + saldo + '</strong> akan dikirimkan ke SIM Baru.<br><br><span class="text-danger fw-bold">PERHATIAN:</span> Saldo santri di kelas ini pada aplikasi lama otomatis menjadi <strong>Rp 0 (Tutup Buku)</strong> dan tercatat ke <strong>Arsip Saldo</strong>.';

            Swal.fire({
                title: title,
                html: htmlMsg,
                icon: isClosed ? 'info' : 'warning',
                showCancelButton: true,
                confirmButtonColor: '#009ef7',
                cancelButtonColor: '#7e8299',
                confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Ya, Jalankan Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#hidden_classroom_id').val(classId);
                    $('#hidden_new_app_url').val($('#input_new_app_url').val());
                    $('#hidden_migration_token').val($('#input_migration_token').val());
                    
                    Swal.fire({
                        title: 'Sedang Memproses Migrasi...',
                        html: 'Mohon tunggu sebentar, sistem sedang mentransfer snapshot data dan mengeksekusi penutupan buku.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $('#form-send-migration').submit();
                }
            });
        });

        // Reverse Action
        $(document).on('click', '.btn-reverse-class', function(e) {
            e.stopPropagation();
            var classId = $(this).data('class-id');
            var className = $(this).data('class-name');
            var students = $(this).data('students');

            Swal.fire({
                title: 'Tarik Balik Saldo Kelas ' + className + '?',
                html: 'Sistem akan mengambil saldo berjalan real-time terkini dari <strong>SIM Baru</strong> untuk ' + students + ' santri di kelas <strong>' + className + '</strong> dan memulihkannya kembali ke aplikasi lama.<br><br>Status kelas akan dibuka kembali.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f1416c',
                cancelButtonColor: '#7e8299',
                confirmButtonText: '<i class="fas fa-undo me-1"></i> Ya, Tarik Balik!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#reverse_hidden_classroom_id').val(classId);
                    $('#reverse_hidden_new_app_url').val($('#input_new_app_url').val());
                    $('#reverse_hidden_migration_token').val($('#input_migration_token').val());

                    Swal.fire({
                        title: 'Sedang Menghubungi SIM Baru...',
                        html: 'Mengambil data saldo berjalan real-time...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $('#form-reverse-migration').submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
