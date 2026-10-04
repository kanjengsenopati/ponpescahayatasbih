<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationSetting;
use App\Models\Classroom;
use App\Models\SaldoHistory;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class MigrationSaldoController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || (!$user->can('Manage Pengaturan Aplikasi') && !$user->hasRole('SUPER ADMIN'))) {
                abort(403, 'Akses terbatas untuk Administrator / Pengelola Saldo.');
            }
            return $next($request);
        });
    }

    /**
     * Halaman Utama Submenu Migrasi Saldo & Tutup Buku
     */
    public function index()
    {
        $applicationSetting = ApplicationSetting::first();
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();

        $totalActiveStudents = Student::whereNull('deleted_at')->where('status', 'ACTIVE')->count();
        $totalActiveSaldo = (int) Student::whereNull('deleted_at')->where('status', 'ACTIVE')->sum('saldo');

        // Ambil daftar seluruh kelas beserta santri aktif dan saldonya
        $classrooms = Classroom::with('school')
            ->withCount(['students' => function ($q) {
                $q->whereNull('deleted_at')->where('status', 'ACTIVE');
            }])
            ->withSum(['students as total_saldo' => function ($q) {
                $q->whereNull('deleted_at')->where('status', 'ACTIVE');
            }], 'saldo')
            ->withSum(['students as total_saving' => function ($q) {
                $q->whereNull('deleted_at')->where('status', 'ACTIVE');
            }], 'saving')
            ->having('students_count', '>', 0)
            ->orderBy('name')
            ->get();

        $unassignedCount = Student::whereNull('classroom_id')->whereNull('deleted_at')->where('status', 'ACTIVE')->count();
        $unassignedSaldo = (int) Student::whereNull('classroom_id')->whereNull('deleted_at')->where('status', 'ACTIVE')->sum('saldo');
        $unassignedSaving = (int) Student::whereNull('classroom_id')->whereNull('deleted_at')->where('status', 'ACTIVE')->sum('saving');

        $migratedClassroomIds = [];
        if ($applicationSetting && is_array($applicationSetting->migrated_classrooms)) {
            $migratedClassroomIds = array_keys($applicationSetting->migrated_classrooms);
        }

        $historyMigratedIds = SaldoHistory::where('description', 'like', '%Penutupan Buku%')
            ->join('students', 'saldo_histories.student_id', '=', 'students.id')
            ->whereNotNull('students.classroom_id')
            ->pluck('students.classroom_id')
            ->unique()
            ->toArray();

        $migratedClassroomIds = array_values(array_unique(array_merge($migratedClassroomIds, $historyMigratedIds)));

        return view('admins.migration-saldo.index', compact(
            'applicationSetting',
            'roles',
            'totalActiveStudents',
            'totalActiveSaldo',
            'classrooms',
            'unassignedCount',
            'unassignedSaldo',
            'unassignedSaving',
            'migratedClassroomIds'
        ));
    }

    /**
     * Simpan pengaturan kunci login & parameter koneksi SIM Baru
     */
    public function saveSettings(Request $request)
    {
        $setting = ApplicationSetting::first() ?: new ApplicationSetting();

        $setting->is_login_locked = $request->boolean('is_login_locked');
        $setting->allowed_roles_when_locked = $request->input('allowed_roles_when_locked', ['SUPER ADMIN', 'Bendahara SMP', 'BENDAHARA MA']);
        $setting->login_locked_message = $request->input('login_locked_message');
        if ($request->filled('new_app_url')) {
            $setting->new_app_url = rtrim($request->input('new_app_url'), '/');
        }
        if ($request->filled('migration_token')) {
            $setting->migration_token = $request->input('migration_token');
        }
        $setting->save();

        return redirect()->route('migration-saldo.index')->with('success', 'Pengaturan Kunci Login & Parameter Migrasi berhasil disimpan.');
    }

    /**
     * Paksa logout seluruh wali santri (cabut token mobile & sesi web)
     */
    public function kickWali(Request $request)
    {
        $revokedTokens = 0;
        if (\Illuminate\Support\Facades\Schema::hasTable('oauth_access_tokens')) {
            $revokedTokens = DB::table('oauth_access_tokens')->where('revoked', 0)->update(['revoked' => 1]);
            if (\Illuminate\Support\Facades\Schema::hasTable('oauth_refresh_tokens')) {
                DB::table('oauth_refresh_tokens')->where('revoked', 0)->update(['revoked' => 1]);
            }
        }

        $sessionPath = storage_path('framework/sessions');
        $clearedSessions = 0;
        if (is_dir($sessionPath)) {
            $files = glob($sessionPath . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    if (@unlink($file)) {
                        $clearedSessions++;
                    }
                }
            }
        }

        $setting = ApplicationSetting::first();
        if ($setting) {
            $setting->is_login_locked = true;
            $setting->save();
        }

        return redirect()->route('migration-saldo.index')->with('success', "Seluruh wali santri berhasil di-logout otomatis ({$revokedTokens} token mobile dicabut, {$clearedSessions} sesi web browser dibersihkan).");
    }

    /**
     * Endpoint data santri per kelas untuk Nested Table Expandable Panel
     * Mengembalikan kolom ARSIP SALDO (saldo sebelum tutup buku) dan SALDO SEKARANG
     */
    public function classroomStudents(Request $request)
    {
        $classroomId = $request->input('classroom_id');

        $query = Student::with('classroom')
            ->whereNull('deleted_at')
            ->where('status', 'ACTIVE')
            ->select('id', 'nis', 'name', 'classroom_id', 'saldo', 'saving');

        if ($classroomId === 'unassigned') {
            $query->whereNull('classroom_id');
            $className = 'Tanpa Kelas';
            $schoolName = '-';
        } else {
            $classroom = Classroom::with('school')->find($classroomId);
            if (!$classroom) {
                return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan'], 404);
            }
            $className = $classroom->name;
            $schoolName = $classroom->school?->name ?? '-';
            $query->where('classroom_id', $classroomId);
        }

        $students = $query->orderBy('name', 'asc')->get();

        $setting = ApplicationSetting::first();
        $migratedClassroomIds = is_array($setting?->migrated_classrooms) ? array_keys($setting->migrated_classrooms) : [];
        $isClosed = in_array((string)$classroomId, $migratedClassroomIds);

        // Ambil riwayat penutupan buku untuk santri
        $studentIds = $students->pluck('id');
        $closingHistories = SaldoHistory::whereIn('student_id', $studentIds)
            ->where('description', 'like', '%Penutupan Buku: Migrasi Saldo%')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('student_id')
            ->keyBy('student_id');

        return response()->json([
            'status' => 'success',
            'classroom_id' => $classroomId,
            'classroom_name' => $className,
            'school_name' => $schoolName,
            'is_closed' => $isClosed,
            'total_students' => $students->count(),
            'total_saldo' => (int) $students->sum('saldo'),
            'total_saving' => (int) $students->sum('saving'),
            'students' => $students->map(function ($s, $idx) use ($isClosed, $closingHistories) {
                $lastClosing = $closingHistories->get($s->id);
                // Jika sudah pernah tutup buku, ambil balance_before (saldo terakhir sebelum di-0-kan)
                $archiveSaldo = $lastClosing ? (int) $lastClosing->balance_before : ($isClosed ? 0 : null);
                $closedAt = $lastClosing ? $lastClosing->created_at->translatedFormat('d/m/Y H:i') : null;

                return [
                    'no' => $idx + 1,
                    'id' => (string) $s->id,
                    'nis' => (string) ($s->nis ?? '-'),
                    'name' => (string) $s->name,
                    'archive_saldo' => $archiveSaldo,
                    'current_saldo' => (int) $s->saldo,
                    'saving' => (int) $s->saving,
                    'is_closed' => $isClosed || $s->saldo == 0,
                    'closed_at' => $closedAt,
                    'closing_note' => $lastClosing?->description,
                ];
            }),
        ]);
    }

    /**
     * Endpoint untuk memuat riwayat mutasi santri secara instan ke Modal
     */
    public function studentMutations(Request $request)
    {
        $studentId = $request->input('student_id');
        $student = Student::with('classroom')->findOrFail($studentId);

        $mutations = SaldoHistory::where('student_id', $studentId)
            ->latest()
            ->take(15)
            ->get();

        $closingMutation = $mutations->first(function ($m) {
            return str_contains($m->description, 'Penutupan Buku: Migrasi Saldo');
        });

        return response()->json([
            'status' => 'success',
            'student' => [
                'id' => (string) $student->id,
                'name' => (string) $student->name,
                'nis' => (string) ($student->nis ?? '-'),
                'classroom' => (string) ($student->classroom?->name ?? '-'),
                'current_saldo' => (int) $student->saldo,
                'archive_saldo' => $closingMutation ? (int) $closingMutation->balance_before : null,
                'closed_at' => $closingMutation ? $closingMutation->created_at->translatedFormat('d/m/Y H:i') : null,
                'closing_note' => $closingMutation?->description,
            ],
            'mutations' => $mutations->map(function ($m) {
                return [
                    'date' => $m->created_at->translatedFormat('d/m/Y H:i'),
                    'type' => $m->type,
                    'amount' => (int) $m->amount,
                    'balance_before' => (int) $m->balance_before,
                    'balance_after' => (int) $m->balance_after,
                    'description' => (string) $m->description,
                    'status' => (string) $m->status,
                ];
            }),
        ]);
    }

    /**
     * Kirim data snapshot saldo per kelas ke SIM Baru dan lakukan Tutup Buku di SIM Lama
     */
    public function sendMigration(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|string',
        ]);

        $setting = ApplicationSetting::first();
        $targetUrl = rtrim($request->input('new_app_url', $setting?->new_app_url ?: 'https://aplikasi.cahayatasbih.or.id'), '/');
        $token = $request->input('migration_token', $setting?->migration_token ?: 'cahaya-tasbih-migration-secret');

        if ($setting) {
            $setting->update([
                'new_app_url' => $targetUrl,
                'migration_token' => $token,
            ]);
        }

        $classroomId = $request->input('classroom_id');
        if ($classroomId === 'unassigned') {
            $classroomName = 'Tanpa Kelas';
            $studentsModel = Student::whereNull('classroom_id')
                ->whereNull('deleted_at')
                ->where('status', 'ACTIVE')
                ->select('id', 'nis', 'nisn', 'name', 'classroom_id', 'saldo', 'saving')
                ->get();
        } else {
            $classroom = Classroom::findOrFail($classroomId);
            $classroomName = $classroom->name;
            $studentsModel = Student::where('classroom_id', $classroom->id)
                ->whereNull('deleted_at')
                ->where('status', 'ACTIVE')
                ->select('id', 'nis', 'nisn', 'name', 'classroom_id', 'saldo', 'saving')
                ->get();
        }

        if ($studentsModel->isEmpty()) {
            return redirect()->back()->with('error', "Tidak ada santri aktif di kelas {$classroomName}.");
        }

        $students = $studentsModel->map(function ($s) use ($classroomName) {
            return [
                'id' => (string) $s->id,
                'nis' => (string) ($s->nis ?? ''),
                'nisn' => (string) ($s->nisn ?? ''),
                'name' => (string) $s->name,
                'classroom' => $classroomName,
                'saldo' => (int) $s->saldo,
                'saving' => (int) $s->saving,
            ];
        })->toArray();

        $totalStudents = count($students);
        $totalSaldo = array_sum(array_column($students, 'saldo'));
        $totalSaving = array_sum(array_column($students, 'saving'));

        $payload = [
            'migration_token' => $token,
            'sent_by' => Auth::user()->name,
            'sent_at' => now()->toDateTimeString(),
            'classroom_id' => (string) $classroomId,
            'classroom_name' => $classroomName,
            'batch_title' => "Kelas {$classroomName}",
            'total_students' => $totalStudents,
            'total_saldo' => $totalSaldo,
            'total_saving' => $totalSaving,
            'students' => $students,
        ];

        try {
            $endpoint = $targetUrl . '/api/internal/migration/receive-saldo';
            $response = Http::timeout(60)->post($endpoint, $payload);

            if ($response->successful()) {
                $resData = $response->json();
                $batchId = $resData['batch_id'] ?? '-';

                // ==========================================
                // EKSEKUSI TUTUP BUKU DI APLIKASI LAMA:
                // Saldo santri di-nol-kan dan dicatat ke riwayat transaksi
                // ==========================================
                DB::transaction(function () use ($studentsModel, $classroomName, $batchId) {
                    $now = now();
                    foreach ($studentsModel as $s) {
                        $currentSaldo = (int) $s->saldo;
                        if ($currentSaldo != 0) {
                            SaldoHistory::create([
                                'id' => (string) Str::uuid(),
                                'student_id' => $s->id,
                                'type' => $currentSaldo > 0 ? SaldoHistory::TYPE_OUT : SaldoHistory::TYPE_IN,
                                'amount' => abs($currentSaldo),
                                'description' => "Penutupan Buku: Migrasi Saldo ke SIM Baru (Kelas {$classroomName} - Ref Batch: {$batchId})",
                                'status' => SaldoHistory::STATUS_SUCCESS,
                                'usage' => SaldoHistory::USAGE_BILL,
                                'balance_before' => $currentSaldo,
                                'balance_after' => 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);

                            $s->update(['saldo' => 0]);
                        }
                    }
                });

                if ($setting) {
                    $currentMigrated = is_array($setting->migrated_classrooms) ? $setting->migrated_classrooms : [];
                    $currentMigrated[$classroomId] = [
                        'name' => $classroomName,
                        'sent_at' => now()->toDateTimeString(),
                        'sent_by' => Auth::user()->name,
                        'total_students' => $totalStudents,
                        'total_saldo' => $totalSaldo,
                        'batch_id' => $batchId,
                    ];

                    $setting->update([
                        'last_migration_sent_at' => now(),
                        'migrated_classrooms' => $currentMigrated,
                    ]);
                }

                return redirect()->route('migration-saldo.index')->with('success', "✅ BERHASIL! Data Kelas {$classroomName} ({$totalStudents} santri, Total Saldo: Rp " . number_format($totalSaldo, 0, ',', '.') . ") telah dikirim ke Aplikasi Baru (Batch Ref: {$batchId}) dan saldo di aplikasi lama telah resmi DITUTUP BUKU (menjadi Rp 0). Seluruh riwayat transaksi tetap tersimpan utuh di Arsip Saldo.");
            } else {
                $err = $response->json()['message'] ?? $response->body() ?? 'Server aplikasi baru menolak request';
                return redirect()->route('migration-saldo.index')->with('error', "Gagal mengirim data migrasi (HTTP {$response->status()}): " . Str::limit($err, 150));
            }
        } catch (\Throwable $e) {
            return redirect()->route('migration-saldo.index')->with('error', "Koneksi ke aplikasi baru gagal: " . $e->getMessage());
        }
    }

    /**
     * Failback / Migrasi Balik: Mengambil saldo berjalan real-time dari SIM Baru dan memulihkan ke SIM Lama
     */
    public function reverseMigration(Request $request)
    {
        $classroomId = $request->input('classroom_id');
        $setting = ApplicationSetting::first();
        $targetUrl = rtrim($request->input('new_app_url') ?: ($setting?->new_app_url ?: 'https://aplikasi.cahayatasbih.or.id'), '/');
        $token = $request->input('migration_token') ?: ($setting?->migration_token ?: 'cahaya-tasbih-migration-secret');

        if ($classroomId === 'unassigned') {
            $classroomName = 'Tanpa Kelas';
            $studentsQuery = Student::whereNull('classroom_id');
        } else {
            $classroom = Classroom::findOrFail($classroomId);
            $classroomName = $classroom->name;
            $studentsQuery = Student::where('classroom_id', $classroom->id);
        }

        $localStudents = $studentsQuery->whereNull('deleted_at')->where('status', 'ACTIVE')->get();
        if ($localStudents->isEmpty()) {
            return redirect()->back()->with('error', "Tidak ada santri aktif di kelas {$classroomName}.");
        }

        try {
            $endpoint = $targetUrl . '/api/internal/migration/export-current-saldo';
            $response = Http::timeout(60)->post($endpoint, [
                'migration_token' => $token,
                'classroom_name' => $classroomName,
                'student_nises' => $localStudents->pluck('nis')->filter()->values()->toArray(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $remoteStudents = $data['students'] ?? [];

                if (empty($remoteStudents)) {
                    return redirect()->route('migration-saldo.index')->with('error', "Server Aplikasi Baru tidak menemukan data saldo berjalan untuk kelas {$classroomName}.");
                }

                $remoteByKey = [];
                foreach ($remoteStudents as $rs) {
                    if (!empty($rs['nis'])) {
                        $remoteByKey['nis_' . $rs['nis']] = $rs;
                    }
                    if (!empty($rs['name'])) {
                        $remoteByKey['name_' . strtolower(trim($rs['name']))] = $rs;
                    }
                }

                DB::transaction(function () use ($localStudents, $remoteByKey, $classroomName) {
                    $now = now();
                    foreach ($localStudents as $ls) {
                        $matched = null;
                        if ($ls->nis && isset($remoteByKey['nis_' . $ls->nis])) {
                            $matched = $remoteByKey['nis_' . $ls->nis];
                        } elseif (isset($remoteByKey['name_' . strtolower(trim($ls->name))])) {
                            $matched = $remoteByKey['name_' . strtolower(trim($ls->name))];
                        }

                        if ($matched) {
                            $newSaldo = (int) ($matched['saldo'] ?? 0);
                            $oldSaldo = (int) $ls->saldo;
                            $diff = $newSaldo - $oldSaldo;

                            if ($diff != 0) {
                                SaldoHistory::create([
                                    'id' => (string) Str::uuid(),
                                    'student_id' => $ls->id,
                                    'type' => $diff > 0 ? SaldoHistory::TYPE_IN : SaldoHistory::TYPE_OUT,
                                    'amount' => abs($diff),
                                    'description' => "Failback / Pemulihan Saldo dari SIM Baru (Kelas {$classroomName})",
                                    'status' => SaldoHistory::STATUS_SUCCESS,
                                    'usage' => SaldoHistory::USAGE_BILL,
                                    'balance_before' => $oldSaldo,
                                    'balance_after' => $newSaldo,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);

                                $ls->update(['saldo' => $newSaldo]);
                            }
                        }
                    }
                });

                if ($setting && is_array($setting->migrated_classrooms)) {
                    $migrated = $setting->migrated_classrooms;
                    unset($migrated[$classroomId]);
                    $setting->update(['migrated_classrooms' => $migrated]);
                }

                return redirect()->route('migration-saldo.index')->with('success', "✅ REVERSE SUKSES! Saldo berjalan real-time dari SIM Baru untuk kelas {$classroomName} telah berhasil dipulihkan kembali ke SIM Lama, dan status kelas dibuka kembali.");
            } else {
                $err = $response->json()['message'] ?? $response->body() ?? 'Server aplikasi baru menolak request';
                return redirect()->route('migration-saldo.index')->with('error', "Gagal mengambil data dari SIM Baru (HTTP {$response->status()}): " . Str::limit($err, 150));
            }
        } catch (\Throwable $e) {
            return redirect()->route('migration-saldo.index')->with('error', "Koneksi ke aplikasi baru gagal: " . $e->getMessage());
        }
    }

    /**
     * DataTables endpoint untuk rincian data santri dan saldonya (pencarian global)
     */
    public function studentData(Request $request)
    {
        $students = Student::with('classroom')
            ->whereNull('deleted_at')
            ->where('status', 'ACTIVE')
            ->select('id', 'nis', 'name', 'classroom_id', 'saldo', 'saving');

        if ($request->filled('classroom_id')) {
            if ($request->classroom_id === 'unassigned') {
                $students->whereNull('classroom_id');
            } else {
                $students->where('classroom_id', $request->classroom_id);
            }
        }

        return DataTables::of($students)
            ->addIndexColumn()
            ->addColumn('classroom_name', fn($s) => $s->classroom?->name ?? 'Tanpa Kelas')
            ->editColumn('saldo', function ($s) {
                if ($s->saldo > 0) {
                    return '<span class="fw-bold text-success font-mono">Rp ' . number_format($s->saldo, 0, ',', '.') . '</span>';
                } elseif ($s->saldo < 0) {
                    return '<span class="fw-bold text-danger font-mono">Rp ' . number_format($s->saldo, 0, ',', '.') . '</span>';
                }
                return '<span class="text-muted fw-bold font-mono">Rp 0 <span class="badge badge-light-success fs-9 ms-1">Tutup Buku</span></span>';
            })
            ->editColumn('saving', fn($s) => 'Rp ' . number_format($s->saving, 0, ',', '.'))
            ->rawColumns(['saldo'])
            ->make(true);
    }
}
