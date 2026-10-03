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
                                            <h3 class="text-gray-900 fw-bolder mb-1">Kirim Data Snapshot Saldo ke Aplikasi Baru</h3>
                                            <span class="text-muted fs-7">Mengirimkan saldo santri aktif secara utuh ke Aplikasi Baru. Di Aplikasi Baru akan tampil ringkasan dan konfirmasi sebelum diterapkan.</span>
                                        </div>
                                    </div>

                                    <!--begin::Stat Ringkasan-->
                                    <div class="row g-4 mb-6">
                                        <div class="col-md-4">
                                            <div class="bg-white p-4 rounded border text-center">
                                                <div class="text-muted fs-7 fw-bold">TOTAL SANTRI AKTIF</div>
                                                <div class="fs-2x fw-bolder text-primary mt-1">{{ number_format($totalActiveStudents ?? 0, 0, ',', '.') }}</div>
                                                <div class="text-muted fs-8">Santri Status ACTIVE</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="bg-white p-4 rounded border text-center">
                                                <div class="text-muted fs-7 fw-bold">TOTAL SALDO ACUAN</div>
                                                <div class="fs-2x fw-bolder text-success mt-1">Rp {{ number_format($totalActiveSaldo ?? 0, 0, ',', '.') }}</div>
                                                <div class="text-muted fs-8">100% Sesuai Laporan Saldo Lama</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
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

                                    <form action="{{ route('application-setting.send-migration') }}" method="POST" id="form-send-migration">
                                        @csrf
                                        <div class="row g-4 mb-6">
                                            <div class="col-md-8">
                                                <label class="fs-7 fw-bold text-gray-700 form-label">URL Server Aplikasi Baru:</label>
                                                <input type="text" class="form-control form-control-solid fs-7" name="new_app_url"
                                                    value="{{ $applicationSetting?->new_app_url ?? 'https://sim.cahayatasbih.or.id' }}" required />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="fs-7 fw-bold text-gray-700 form-label">Secret Token Migrasi:</label>
                                                <input type="text" class="form-control form-control-solid fs-7" name="migration_token"
                                                    value="{{ $applicationSetting?->migration_token ?? 'cahaya-tasbih-migration-secret' }}" required />
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted fs-7">
                                                <i class="fas fa-info-circle text-primary me-1"></i>
                                                Aplikasi Baru tidak langsung menimpa data, melainkan menunggu konfirmasi persetujuan dari Admin di Aplikasi Baru.
                                            </span>
                                            <button type="submit" class="btn btn-primary" onclick="return confirm('Kirim data saldo {{ number_format($totalActiveStudents ?? 0, 0, \',\', \'.\') }} santri (Total: Rp {{ number_format($totalActiveSaldo ?? 0, 0, \',\', \'.\') }}) ke Aplikasi Baru untuk ditinjau?');">
                                                <i class="fas fa-paper-plane me-2"></i>
                                                Kirim Data Saldo ke Aplikasi Baru
                                            </button>
                                        </div>
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
@push('js')
<script>
    $('.time').mask('00:00', {
        reverse: true
        });
</script>
@endpush