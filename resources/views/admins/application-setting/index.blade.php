@extends('layouts.master', ['title' => 'Setting Aplikasi'])
@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <!--begin::Title-->
                <h1 class="d-flex text-dark fw-bolder fs-3 align-items-center my-1">Setting Aplikasi</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-300 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <!--begin::Item-->

                    <!--end::Item-->
                    <!--begin::Item-->
                    <a class="breadcrumb-item" href="{{ route('application-setting.index') }}">
                        <li class="breadcrumb-item text-muted">Setting Aplikasi</li>
                    </a>
                    <!--end::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-300 w-5px h-2px"></span>
                    </li>
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-dark">
                        <span class="text-muted fw-bolder fs-7">Edit Setting</span>
                        <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->

        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-fluid">
            <!--begin::Contacts App- Add New Contact-->
            <div class="row g-7">
                <!--begin::Content-->
                <div class="col-xl-12">
                    <!--begin::Contacts-->
                    <div class="card card-flush h-lg-100" id="kt_contacts_main">
                        <!--begin::Card header-->

                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-7">
                            <!--begin::Form-->
                            <x-alert.alert-validation />
                            <form action="{{ route('application-setting.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <x-form.put-method />

                                {{-- add icon kontak aplikasi --}}
                                <div class="row mb-6">
                                    <div class="col-6">
                                        <a href="{{ route('contact.index') }}" class="btn btn-sm btn-light-primary">
                                            <i class="fas fa-user-circle fs-1 text-primary"></i>Data Kontak Aplikasi
                                        </a>
                                    </div>
                                </div>
                                <div class="row mb-6">
                                    <div class="col-6">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-bold form-label" for="payment_fee">
                                            <span class="required">Fee Xendit</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Fee Pembayaran"></i>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control form-control-solid"
                                                id="payment_fee" name="payment_fee" placeholder="Masukkan Fee Xendit"
                                                value="{{ @$applicationSetting->payment_fee ?? old('payment_fee') }}"
                                                required />
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-bold form-label" for="bill_fee">
                                            <span class="required">Fee Tagihan</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Fee Pembayaran"></i>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control form-control-solid" id="bill_fee"
                                                name="bill_fee" placeholder="Masukkan Fee Tagihan"
                                                value="{{ @$applicationSetting->bill_fee ?? old('bill_fee') }}"
                                                required />
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-6">
                                    <div class="col-6">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-bold form-label" for="saldo_fee">
                                            <span class="required">Fee Saldo</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Fee Pembayaran"></i>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control form-control-solid" id="saldo_fee"
                                                name="saldo_fee" placeholder="Masukkan Fee Saldo"
                                                value="{{ @$applicationSetting->saldo_fee ?? old('saldo_fee') }}"
                                                required />
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <label class="fs-6 fw-bold form-label" for="payment_expire_time">
                                            <span class="required">Waktu Kadaluarsa Pembayaran</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Waktu Kadaluarsa Pembayaran"></i>
                                        </label>
                                        <input type="text" placeholder="hh:mm"
                                            class="form-control form-control-solid time" id="payment_expire_time"
                                            name="payment_expire_time"
                                            placeholder="Masukkan Waktu Kadaluarsa Pembayaran"
                                            value="{{ @$applicationSetting->payment_expire_time ?? old('payment_expire_time') }}"
                                            required />
                                    </div>
                                </div>

                                {{-- <div class="row mb-6">
                                    <div class="col-6">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-bold form-label" for="target_month">
                                            <span class="required">Target Bulanan</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Target Bulanan"></i>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control form-control-solid input-money"
                                                id="target_month" name="target_month"
                                                placeholder="Masukkan Target Pembayaran Bulanan"
                                                value="{{ @$applicationSetting->target_month ?? old('target_month') }}"
                                                required />
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-bold form-label" for="target_year">
                                            <span class="required">Target Tahunan</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Target Tahunan"></i>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control form-control-solid input-money"
                                                id="target_year" name="target_year"
                                                placeholder="Masukkan Target Pembayaran Tahunan"
                                                value="{{ @$applicationSetting->target_year ?? old('target_year') }}"
                                                required />
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="row mb-6">
                                    <div class="col-6">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-bold form-label" for="link_whatsapp">
                                            <span class="required">Link Whatsapp Gateway</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Link Whatsapp Gateway"></i>
                                        </label>
                                        <input type="url" class="form-control form-control-solid" id="link_whatsapp"
                                            name="link_whatsapp" placeholder="Masukkan Link Whatsapp Gateway"
                                            value="{{ @$applicationSetting->link_whatsapp ?? old('link_whatsapp') }}"
                                            required />
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <!--end::Input-->
                                    </div>
                                    <div class="col-6">
                                        <label class="fs-6 fw-bold form-label" for="number_whatsapp">
                                            <span class="required">Nomor Whatsapp</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Nomor Whatsapp Gateway"></i>
                                        </label>
                                        <input type="number" class="form-control form-control-solid"
                                            id="number_whatsapp" name="number_whatsapp"
                                            placeholder="Masukkan Nomor Whatsapp Gateway"
                                            value="{{ @$applicationSetting->number_whatsapp ?? old('number_whatsapp') }}"
                                            required />
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <div class="col-6">
                                        <label class="fs-6 fw-bold form-label" for="api_key">
                                            <span class="required">Status Device ID</span>
                                            {{-- add info text with color red or green --}}
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Status Device ID"></i>
                                        </label>
                                        {{-- no input only info --}}
                                        <div class="form-control form-control-solid">
                                            <span
                                                class="badge badge-{{ @$applicationSetting->whatsapp_status ? 'success' : 'danger' }}">
                                                {{ @$applicationSetting->whatsapp_status ? 'Aktif' : 'Tidak Aktif'
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="fs-6 fw-bold form-label" for="device_id">
                                            <span class="required">Device ID</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan device id yang diperolah dari aplikasi whatsapp gateway"></i>
                                        </label>
                                        <input type="text" class="form-control form-control-solid" id="device_id"
                                            name="device_id" placeholder="Masukkan Device ID"
                                            value="{{ @$applicationSetting->device_id ?? old('device_id') }}"
                                            required />
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <!--end::Input-->
                                    </div>
                                </div>

                                <div class="fv-row mb-6">
                                    <!--begin::Label-->
                                    <div class="fv-row mb-6">
                                        <x-form.image-upload label="Background Kartu Siswa" name="student_card_image"
                                            :value="@$applicationSetting->student_card_image ?? null" />
                                    </div>
                                </div>
                                <div class="separator separator-dashed my-8"></div>

                                <!--begin::Section Kunci Login-->
                                <div class="mb-10 bg-light-danger p-6 rounded border border-danger border-dashed">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-danger text-white">
                                                <i class="fas fa-user-lock fs-2 text-white"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h3 class="text-gray-900 fw-bolder mb-1">Kunci Akses Login Sistem (Cut-Off Saldo & Migrasi)</h3>
                                            <span class="text-muted fs-7">Gunakan fitur ini untuk membekukan transaksi agar saldo santri tidak bergerak saat proses migrasi ke aplikasi baru.</span>
                                        </div>
                                    </div>

                                    <!--begin::Toggle Switch-->
                                    <div class="d-flex align-items-center justify-content-between p-4 mb-6 bg-white rounded border">
                                        <div>
                                            <label class="fs-6 fw-bold text-gray-800" for="is_login_locked">
                                                Aktifkan Kunci Login Sistem
                                            </label>
                                            <div class="text-muted fs-7">Jika aktif, kasir toko/koperasi, piket, TU, dan wali santri otomatis tidak bisa login.</div>
                                        </div>
                                        <div class="form-check form-switch form-check-custom form-check-danger form-check-solid">
                                            <input class="form-check-input h-30px w-50px" type="checkbox" name="is_login_locked" id="is_login_locked" value="1"
                                                {{ !empty($applicationSetting?->is_login_locked) ? 'checked' : '' }} />
                                        </div>
                                    </div>
                                    <!--end::Toggle Switch-->

                                    <!--begin::Allowed Roles-->
                                    <div class="mb-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            <i class="fas fa-shield-alt text-primary me-1"></i>
                                            Role yang TETAP Boleh Login Saat Dikunci:
                                        </label>
                                        <div class="text-muted fs-7 mb-3">Centang role staf/admin yang diberi dispensasi khusus (misal Bendahara untuk rekapitulasi data):</div>
                                        @php
                                            $allowedRoles = $applicationSetting?->getAllowedRoles() ?? ['SUPER ADMIN', 'Bendahara SMP', 'BENDAHARA MA'];
                                        @endphp
                                        <div class="row g-3 bg-white p-4 rounded border">
                                            @foreach ($roles as $role)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check form-check-custom form-check-sm">
                                                    <input class="form-check-input" type="checkbox" name="allowed_roles_when_locked[]"
                                                        value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                        {{ in_array($role->name, $allowedRoles) ? 'checked' : '' }} />
                                                    <label class="form-check-label text-gray-800 fw-bold fs-7 ms-2" for="role_{{ $role->id }}">
                                                        {{ $role->name }}
                                                        @if (str_contains(strtoupper($role->name), 'BENDAHARA') || str_contains(strtoupper($role->name), 'SUPER ADMIN'))
                                                            <span class="badge badge-light-success fs-8 py-0 px-2 ms-1">Disarankan</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!--end::Allowed Roles-->

                                    <!--begin::Pesan Notifikasi Login Ditolak-->
                                    <div class="mb-2">
                                        <label class="fs-6 fw-bold form-label" for="login_locked_message">
                                            <i class="fas fa-comment-alt text-warning me-1"></i>
                                            Pesan Notifikasi Saat User Ditolak Login:
                                        </label>
                                        <textarea class="form-control form-control-solid" id="login_locked_message" name="login_locked_message" rows="3"
                                            placeholder="Contoh: Mohon maaf, sistem aplikasi lama sedang ditutup sementara untuk proses migrasi data ke aplikasi baru. Silakan hubungi Bendahara.">{{ $applicationSetting?->login_locked_message ?? 'Mohon maaf, sistem aplikasi lama sedang ditutup sementara untuk proses migrasi data ke aplikasi baru. Silakan hubungi Bendahara / Administrator.' }}</textarea>
                                        <div class="text-muted fs-8 mt-1">Pesan ini akan langsung muncul di layar ketika pengguna yang dikunci mencoba login.</div>
                                    </div>
                                    <!--end::Pesan Notifikasi Login Ditolak-->
                                </div>
                                <!--end::Section Kunci Login-->

                                <!--begin::Action buttons-->
                                <div class="d-flex justify-content-end mb-6">
                                    @if (Auth::user()->can('Edit Pengaturan Aplikasi'))
                                    <button type="submit" data-kt-contacts-type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        <span class="indicator-label">Simpan Pengaturan</span>
                                        <span class="indicator-progress">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                    @endif
                                </div>
                                <!--end::Action buttons-->
                            </form>
                            <!--end::Form-->

                            <div class="separator separator-dashed my-8"></div>

                            <!--begin::Section Kirim Migrasi Saldo ke Aplikasi Baru-->
                            <div class="card bg-light-primary border border-primary border-dashed">
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-primary text-white">
                                                <i class="fas fa-paper-plane fs-2 text-white"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h3 class="text-gray-900 fw-bolder mb-1">Migrasi Saldo & Tutup Buku Per Kelas</h3>
                                            <span class="text-muted fs-7">Kirim data saldo santri per kelas ke Aplikasi Baru dan lakukan Tutup Buku di aplikasi lama secara aman.</span>
                                        </div>
                                    </div>

                                    <!--begin::Banner Informasi Tutup Buku-->
                                    <div class="alert alert-warning d-flex align-items-center p-5 mb-6 rounded border border-warning bg-white">
                                        <i class="fas fa-exclamation-triangle fs-2hx text-warning me-4"></i>
                                        <div class="d-flex flex-column">
                                            <h5 class="mb-1 text-warning fw-bolder">Ketentuan Penting Tutup Buku & Keamanan Riwayat Saldo:</h5>
                                            <div class="fs-7 text-gray-800">
                                                <ul class="mb-0 ps-4">
                                                    <li><strong>Saldo Menjadi Rp 0:</strong> Setiap kelas yang berhasil dikirim ke Aplikasi Baru akan otomatis mengalami <strong>Tutup Buku</strong> (saldo santri di aplikasi lama berubah menjadi <strong>Rp 0</strong>).</li>
                                                    <li><strong>Riwayat Mutasi Tetap Utuh:</strong> Seluruh riwayat transaksi masa lalu <strong>TIDAK DIHAPUS</strong>. Pengguna yang memiliki wewenang (Super Admin & Bendahara) tetap dapat melihat seluruh riwayat dan mutasi santri di menu <strong>Laporan Saldo / Riwayat Saldo</strong>.</li>
                                                    <li><strong>Pengiriman Sekelas demi Sekelas:</strong> Dilakukan per kelas agar proses ringan, bebas risiko timeout, dan mudah diverifikasi oleh Bendahara.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Banner Informasi Tutup Buku-->

                                    <!--begin::Stat Ringkasan-->
                                    <div class="row g-4 mb-6">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="bg-white p-4 rounded border text-center">
                                                <div class="text-muted fs-7 fw-bold">TOTAL SANTRI AKTIF</div>
                                                <div class="fs-2x fw-bolder text-primary mt-1">{{ number_format($totalActiveStudents ?? 0, 0, ',', '.') }}</div>
                                                <div class="text-muted fs-8">Santri Status ACTIVE</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="bg-white p-4 rounded border text-center">
                                                <div class="text-muted fs-7 fw-bold">TOTAL SALDO ACUAN</div>
                                                <div class="fs-2x fw-bolder text-success mt-1">Rp {{ number_format($totalActiveSaldo ?? 0, 0, ',', '.') }}</div>
                                                <div class="text-muted fs-8">Master Saldo Keseluruhan</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="bg-white p-4 rounded border text-center">
                                                <div class="text-muted fs-7 fw-bold">JUMLAH KELAS / ROMBEL</div>
                                                <div class="fs-2x fw-bolder text-dark mt-1">{{ count($classrooms ?? []) }}</div>
                                                <div class="text-muted fs-8">Kelas Santri Aktif</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="bg-white p-4 rounded border text-center">
                                                <div class="text-muted fs-7 fw-bold">TERAKHIR DIKIRIM</div>
                                                <div class="fs-5 fw-bolder text-gray-800 mt-2">
                                                    {{ !empty($applicationSetting?->last_migration_sent_at) ? \Carbon\Carbon::parse($applicationSetting->last_migration_sent_at)->translatedFormat('d M Y H:i') : 'Belum Pernah' }}
                                                </div>
                                                <div class="text-muted fs-8">Riwayat Kirim Migrasi</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Stat Ringkasan-->

                                    <!--begin::Pengaturan Koneksi Aplikasi Baru-->
                                    <div class="bg-white p-4 rounded border mb-6">
                                        <div class="row g-4 align-items-end">
                                            <div class="col-md-6">
                                                <label class="fs-7 fw-bold text-gray-700 form-label">URL Server Aplikasi Baru:</label>
                                                <input type="text" class="form-control form-control-solid fs-7" id="input_new_app_url"
                                                    value="{{ $applicationSetting?->new_app_url ?? 'https://sim.cahayatasbih.or.id' }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="fs-7 fw-bold text-gray-700 form-label">Secret Token Migrasi:</label>
                                                <input type="text" class="form-control form-control-solid fs-7" id="input_migration_token"
                                                    value="{{ $applicationSetting?->migration_token ?? 'cahaya-tasbih-migration-secret' }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Pengaturan Koneksi Aplikasi Baru-->

                                    <!--begin::Tabel Daftar Kelas untuk Migrasi & Tutup Buku-->
                                    <div class="card card-flush bg-white border mb-6">
                                        <div class="card-header pt-4 pb-2">
                                            <div class="card-title">
                                                <h4 class="fw-bolder text-gray-800">
                                                    <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                                                    Daftar Kelas untuk Migrasi & Tutup Buku
                                                </h4>
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
                                                                    <span class="fw-bold">{{ $index + 1 }}</span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="fw-bolder text-gray-800 fs-6">{{ $c->name }}</span>
                                                            </td>
                                                            <td>{{ $c->school?->name ?? '-' }}</td>
                                                            <td class="text-center">
                                                                <span class="badge badge-light-primary fw-bold">{{ number_format($c->students_count) }} Santri</span>
                                                            </td>
                                                            <td class="text-end fw-bolder {{ $cSaldo < 0 ? 'text-danger' : ($cSaldo > 0 ? 'text-success' : 'text-muted') }}">
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

                                                        <!--begin::Nested Table Expandable Panel (Stay di satu fokus pandangan)-->
                                                        <tr class="nested-expandable-row d-none" id="panel-class-{{ $c->id }}">
                                                            <td colspan="7" class="p-0 border-0 bg-transparent">
                                                                <div class="nested-panel-wrapper p-4 my-2 mx-3 rounded bg-light-primary border border-primary border-dashed shadow-sm">
                                                                    <!-- Header bar matching Image 2 -->
                                                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-gray-300">
                                                                        <div class="d-flex align-items-center gap-3">
                                                                            <span class="bullet bg-primary w-10px h-10px rounded-circle"></span>
                                                                            <span class="fs-6 fw-bolder text-gray-800">Rincian Santri & Saldo Kelas {{ $c->name }} ({{ number_format($c->students_count) }} Santri)</span>
                                                                            <span class="badge badge-light-primary fw-bold px-3 py-1 fs-8">{{ $c->school?->name ?? 'Madrasah' }}</span>
                                                                            @if ($isClosed)
                                                                                <span class="badge badge-light-success fw-bold py-1 px-2 fs-8"><i class="fas fa-check-circle text-success me-1"></i> Sudah Tutup Buku</span>
                                                                            @elseif ($cSaldo > 0)
                                                                                <span class="badge badge-light-warning fw-bold py-1 px-2 fs-8"><i class="fas fa-clock text-warning me-1"></i> Belum Tutup Buku</span>
                                                                            @else
                                                                                <span class="badge badge-light-info fw-bold py-1 px-2 fs-8"><i class="fas fa-info-circle text-info me-1"></i> Saldo Rp 0</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <span class="fs-7 fw-bold text-gray-500 text-uppercase tracking-wider">Total Saldo Kelas:</span>
                                                                            <span class="fs-6 fw-bolder text-success">Rp {{ number_format($cSaldo, 0, ',', '.') }}</span>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Search & Filter Bar inside Panel -->
                                                                    <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                                                                        <div class="position-relative w-300px">
                                                                            <input type="text" class="form-control form-control-sm form-control-solid ps-9 fs-7 input-search-inner"
                                                                                data-target="inner-tbody-{{ $c->id }}" placeholder="Cari nama santri / NIS di kelas ini..." />
                                                                            <span class="position-absolute top-50 translate-middle-y ms-3 text-gray-400">
                                                                                <i class="fas fa-search fs-8"></i>
                                                                            </span>
                                                                        </div>
                                                                        <div class="text-muted fs-8">
                                                                            <i class="fas fa-info-circle text-primary me-1"></i> Menampilkan seluruh santri aktif dalam kelas ini
                                                                        </div>
                                                                    </div>

                                                                    <!-- Inner Table Container -->
                                                                    <div class="bg-white rounded border shadow-xs table-responsive" style="max-height: 380px; overflow-y: auto;">
                                                                        <table class="table table-sm table-row-dashed align-middle mb-0 gs-3 gy-2 fs-7">
                                                                            <thead class="bg-light sticky-top">
                                                                                <tr class="text-gray-400 fw-bold fs-8 text-uppercase tracking-widest border-bottom">
                                                                                    <th class="ps-3" style="width: 5%">#</th>
                                                                                    <th style="width: 20%">NIS</th>
                                                                                    <th style="width: 45%">NAMA SANTRI</th>
                                                                                    <th class="text-end" style="width: 15%">SALDO UTAMA</th>
                                                                                    <th class="text-center pe-3" style="width: 15%">STATUS BUKU</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="inner-tbody-{{ $c->id }}" data-loaded="0">
                                                                                <tr>
                                                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                                                        <i class="fas fa-spinner fa-spin me-2 text-primary fs-5"></i> Memuat data santri kelas {{ $c->name }}...
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    <!-- Footer of Expandable Panel with Quick Actions -->
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
                                                            <td colspan="7" class="text-center text-muted py-4">Tidak ada data kelas dengan santri aktif.</td>
                                                        </tr>
                                                        @endforelse

                                                        @if (!empty($unassignedCount) && $unassignedCount > 0)
                                                        @php
                                                            $uSaldo = (int) ($unassignedSaldo ?? 0);
                                                            $uClosed = in_array('unassigned', $migratedClassroomIds ?? []);
                                                        @endphp
                                                        <tr id="row-class-unassigned" class="classroom-main-row bg-light-warning" data-class-id="unassigned">
                                                            <td class="ps-4">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <button type="button" class="btn btn-icon btn-sm btn-light-warning w-22px h-22px rounded-circle btn-toggle-panel"
                                                                        data-class-id="unassigned" data-class-name="Tanpa Kelas" title="Buka/Tutup Rincian Santri">
                                                                        <i class="fas fa-plus fs-9" id="icon-toggle-unassigned"></i>
                                                                    </button>
                                                                    <span class="fw-bold">-</span>
                                                                </div>
                                                            </td>
                                                            <td><span class="fw-bolder text-gray-800 fs-6">Tanpa Kelas (Belum Di-assign)</span></td>
                                                            <td>-</td>
                                                            <td class="text-center">
                                                                <span class="badge badge-light-danger fw-bold">{{ number_format($unassignedCount) }} Santri</span>
                                                            </td>
                                                            <td class="text-end fw-bolder {{ $uSaldo < 0 ? 'text-danger' : ($uSaldo > 0 ? 'text-success' : 'text-muted') }}">
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
                                                                        data-students="{{ $unassignedCount }}"
                                                                        title="Tarik balik saldo real-time dari SIM Baru dan buka kembali kelas">
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
                                                                    <!-- Header bar matching Image 2 -->
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
                                                                            <span class="fs-6 fw-bolder text-success">Rp {{ number_format($uSaldo, 0, ',', '.') }}</span>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Search & Filter Bar inside Panel -->
                                                                    <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                                                                        <div class="position-relative w-300px">
                                                                            <input type="text" class="form-control form-control-sm form-control-solid ps-9 fs-7 input-search-inner"
                                                                                data-target="inner-tbody-unassigned" placeholder="Cari nama santri / NIS..." />
                                                                            <span class="position-absolute top-50 translate-middle-y ms-3 text-gray-400">
                                                                                <i class="fas fa-search fs-8"></i>
                                                                            </span>
                                                                        </div>
                                                                        <div class="text-muted fs-8">
                                                                            <i class="fas fa-info-circle text-warning me-1"></i> Menampilkan seluruh santri aktif tanpa kelas
                                                                        </div>
                                                                    </div>

                                                                    <!-- Inner Table Container -->
                                                                    <div class="bg-white rounded border shadow-xs table-responsive" style="max-height: 380px; overflow-y: auto;">
                                                                        <table class="table table-sm table-row-dashed align-middle mb-0 gs-3 gy-2 fs-7">
                                                                            <thead class="bg-light sticky-top">
                                                                                <tr class="text-gray-400 fw-bold fs-8 text-uppercase tracking-widest border-bottom">
                                                                                    <th class="ps-3" style="width: 5%">#</th>
                                                                                    <th style="width: 20%">NIS</th>
                                                                                    <th style="width: 45%">NAMA SANTRI</th>
                                                                                    <th class="text-end" style="width: 15%">SALDO UTAMA</th>
                                                                                    <th class="text-center pe-3" style="width: 15%">STATUS BUKU</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="inner-tbody-unassigned" data-loaded="0">
                                                                                <tr>
                                                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                                                        <i class="fas fa-spinner fa-spin me-2 text-warning fs-5"></i> Memuat data santri tanpa kelas...
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    <!-- Footer of Expandable Panel with Quick Actions -->
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
                                                                            @if ($uClosed)
                                                                            <button type="button" class="btn btn-sm btn-light-danger py-1 px-3 btn-reverse-class"
                                                                                data-class-id="unassigned"
                                                                                data-class-name="Tanpa Kelas"
                                                                                data-students="{{ $unassignedCount }}">
                                                                                <i class="fas fa-undo-alt me-1"></i> Tarik Balik dari SIM Baru
                                                                            </button>
                                                                            @endif
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

                                    <!--begin::Pencarian Global Santri (Collapsible - Opsional)-->
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

                                    <!-- Hidden form for sending migration per class -->
                                    <form action="{{ route('application-setting.send-migration') }}" method="POST" id="form-send-migration" class="d-none">
                                        @csrf
                                        <input type="hidden" name="classroom_id" id="hidden_classroom_id" />
                                        <input type="hidden" name="new_app_url" id="hidden_new_app_url" />
                                        <input type="hidden" name="migration_token" id="hidden_migration_token" />
                                    </form>

                                    <!-- Hidden form for failback / reverse migration per class -->
                                    <form action="{{ route('application-setting.reverse-migration') }}" method="POST" id="form-reverse-migration" class="d-none">
                                        @csrf
                                        <input type="hidden" name="classroom_id" id="reverse_hidden_classroom_id" />
                                        <input type="hidden" name="new_app_url" id="reverse_hidden_new_app_url" />
                                        <input type="hidden" name="migration_token" id="reverse_hidden_migration_token" />
                                    </form>

                                </div>
                            </div>
                            <!--end::Section Kirim Migrasi Saldo-->

                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Contacts-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Contacts App- Add New Contact-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
<!--end::Content-->
<!--end::Wrapper-->
@endsection

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
    .inner-students-table tbody tr:hover {
        background-color: #f1f5f9 !important;
    }
    .btn-toggle-panel {
        transition: all 0.2s ease;
    }
</style>

@push('js')
<script>
    $('.time').mask('00:00', {
        reverse: true
    });

    $(document).ready(function() {
        var table = $('#table-migration-preview').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('application-setting.student-data') }}",
                data: function(d) {
                    d.classroom_id = $('#filter_classroom_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'ps-4' },
                { data: 'nis', name: 'nis' },
                { data: 'name', name: 'name', className: 'fw-bold text-gray-800' },
                { data: 'classroom_name', name: 'classroom.name' },
                { data: 'saldo', name: 'saldo', className: 'text-end' },
                { data: 'saving', name: 'saving', className: 'text-end pe-4' }
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

        // Filter dropdown change
        $('#filter_classroom_id').on('change', function() {
            table.ajax.reload();
        });

        // Toggle Nested Expandable Panel (stay di satu fokus pandangan)
        function toggleClassroomPanel(classId) {
            var panelRow = $('#panel-class-' + classId);
            var mainRow = $('#row-class-' + classId);
            var toggleIcon = $('#icon-toggle-' + classId);
            var btnToggle = $('.btn-toggle-panel[data-class-id="' + classId + '"]');

            if (panelRow.hasClass('d-none')) {
                // Show panel
                panelRow.removeClass('d-none');
                mainRow.addClass('is-open');
                toggleIcon.removeClass('fa-plus').addClass('fa-minus');
                btnToggle.addClass('btn-primary text-white').removeClass('btn-light-primary btn-light-warning');

                // Load data via AJAX if not loaded yet
                var tbody = $('#inner-tbody-' + classId);
                if (tbody.data('loaded') != '1') {
                    $.ajax({
                        url: "{{ route('application-setting.classroom-students') }}",
                        type: "GET",
                        data: { classroom_id: classId },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === 'success') {
                                var html = '';
                                if (res.students && res.students.length > 0) {
                                    res.students.forEach(function(s, idx) {
                                        var saldoFormatted = 'Rp ' + Number(s.saldo).toLocaleString('id-ID');
                                        var saldoClass = s.saldo > 0 ? 'text-success fw-bolder' : (s.saldo < 0 ? 'text-danger fw-bolder' : 'text-muted fw-bold');
                                        var statusBadge = s.is_closed 
                                            ? '<span class="badge badge-light-success fs-9 py-1 px-2"><i class="fas fa-check-circle text-success me-1"></i> Sudah Tutup Buku</span>'
                                            : (s.saldo > 0 
                                                ? '<span class="badge badge-light-warning fs-9 py-1 px-2"><i class="fas fa-clock text-warning me-1"></i> Belum Tutup Buku</span>'
                                                : '<span class="badge badge-light-info fs-9 py-1 px-2"><i class="fas fa-info-circle text-info me-1"></i> Saldo Rp 0</span>');

                                        html += '<tr class="student-item-row hover-bg-light" data-search="' + (s.name + ' ' + s.nis).toLowerCase() + '">';
                                        html += '  <td class="ps-3 text-gray-500 font-mono">' + (idx + 1) + '</td>';
                                        html += '  <td class="font-mono text-gray-700 fw-bold">' + (s.nis || '-') + '</td>';
                                        html += '  <td><span class="fw-bold text-gray-800">' + s.name + '</span></td>';
                                        html += '  <td class="text-end font-mono ' + saldoClass + '">' + saldoFormatted + '</td>';
                                        html += '  <td class="text-center pe-3">' + statusBadge + '</td>';
                                        html += '</tr>';
                                    });
                                } else {
                                    html = '<tr><td colspan="5" class="text-center py-4 text-muted fst-italic">Tidak ada santri aktif di kelas ini.</td></tr>';
                                }
                                tbody.html(html);
                                tbody.data('loaded', '1');
                            }
                        },
                        error: function() {
                            tbody.html('<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Gagal memuat data santri. Silakan coba lagi.</td></tr>');
                        }
                    });
                }
            } else {
                // Hide panel
                panelRow.addClass('d-none');
                mainRow.removeClass('is-open');
                toggleIcon.removeClass('fa-minus').addClass('fa-plus');
                btnToggle.removeClass('btn-primary text-white').addClass('btn-light-primary');
            }
        }

        // Click handler on table row (clicking row directly reveals the panel)
        $(document).on('click', '.classroom-main-row', function(e) {
            // Ignore click if targeted on button, link, or input inside row
            if ($(e.target).closest('button, a, input, select').length) {
                return;
            }
            var classId = $(this).data('class-id');
            toggleClassroomPanel(classId);
        });

        // Click handler on circular + button
        $(document).on('click', '.btn-toggle-panel', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var classId = $(this).data('class-id');
            toggleClassroomPanel(classId);
        });

        // Click handler on Sembunyikan Rincian inside panel
        $(document).on('click', '.btn-close-panel', function(e) {
            e.preventDefault();
            var classId = $(this).data('class-id');
            toggleClassroomPanel(classId);
        });

        // Live Search within inner table
        $(document).on('keyup', '.input-search-inner', function() {
            var q = $(this).val().toLowerCase();
            var targetTbodyId = $(this).data('target');
            $('#' + targetTbodyId).find('.student-item-row').each(function() {
                var searchStr = $(this).data('search') || '';
                if (searchStr.indexOf(q) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Button Migrate per Class
        $(document).on('click', '.btn-migrate-class', function() {
            var classId = $(this).data('class-id');
            var className = $(this).data('class-name');
            var students = $(this).data('students');
            var saldo = $(this).data('saldo');
            var isClosed = $(this).data('is-closed') == '1';

            var msg = "⚠️ KONFIRMASI MIGRASI & TUTUP BUKU\n\n" +
                      "Kelas: " + className + "\n" +
                      "Jumlah Santri: " + students + " Santri\n" +
                      "Total Saldo: Rp " + saldo + "\n\n";

            if (isClosed) {
                msg += "Kelas ini sebelumnya SUDAH PERNAH ditutup buku (dimigrasikan).\n" +
                       "Apakah Anda ingin mengirim ulang data snapshot kelas " + className + " ke Aplikasi Baru?";
            } else if (saldo == "0") {
                msg += "Santri di kelas ini memiliki total saldo Rp 0.\n" +
                       "Kirim data santri kelas " + className + " ke Aplikasi Baru agar terdaftar di SIM Baru?";
            } else {
                msg += "PERHATIAN:\n" +
                       "Setelah data kelas ini dikirim ke Aplikasi Baru:\n" +
                       "1. Saldo santri kelas ini di aplikasi lama OTOMATIS MENJADI RP 0 (TUTUP BUKU).\n" +
                       "2. Riwayat mutasi lama TETAP AMAN dan bisa dilihat di Laporan Saldo.\n" +
                       "3. Di Aplikasi Baru saldo akan menunggu konfirmasi persetujuan dari Admin.\n\n" +
                       "Apakah Anda yakin ingin memproses Tutup Buku kelas " + className + "?";
            }

            if (confirm(msg)) {
                $('#hidden_classroom_id').val(classId);
                $('#hidden_new_app_url').val($('#input_new_app_url').val());
                $('#hidden_migration_token').val($('#input_migration_token').val());
                $('#form-send-migration').submit();
            }
        });

        // Button Reverse / Failback per Class
        $(document).on('click', '.btn-reverse-class', function() {
            var classId = $(this).data('class-id');
            var className = $(this).data('class-name');
            var students = $(this).data('students');

            var msg = "⚠️ KONFIRMASI TARIK BALIK (FAILBACK) DARI SIM BARU\n\n" +
                      "Kelas: " + className + " (" + students + " Santri)\n\n" +
                      "Sistem akan menarik SALDO BERJALAN TERKINI dari SIM Baru (termasuk hasil transaksi jajan kantin & top up selama di SIM Baru) untuk dipulihkan ke aplikasi ini.\n\n" +
                      "1. Saldo santri kelas ini akan diperbarui mengikuti saldo real-time SIM Baru.\n" +
                      "2. Mutasi pemulihan saldo otomatis dicatat di riwayat transaksi.\n" +
                      "3. Status kelas ini akan dibuka kembali.\n\n" +
                      "Apakah Anda yakin ingin menarik balik data Kelas " + className + "?";

            if (confirm(msg)) {
                $('#reverse_hidden_classroom_id').val(classId);
                $('#reverse_hidden_new_app_url').val($('#input_new_app_url').val());
                $('#reverse_hidden_migration_token').val($('#input_migration_token').val());
                $('#form-reverse-migration').submit();
            }
        });
    });
</script>
@endpush