<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ApplicationSetting;
use Illuminate\Console\Application;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\Admin\ApplicationSettingRequest;

class ApplicationSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::user()->can('Manage Pengaturan Aplikasi')) {
            return redirect()->back()->with('error', 'Maaf, Anda tidak memiliki akses untuk halaman tersebut');
        }

        $applicationSetting = ApplicationSetting::first();
        $roles = \Spatie\Permission\Models\Role::where('guard_name', 'web')->orderBy('name')->get();
        
        $totalActiveStudents = \App\Models\Student::whereNull('deleted_at')->where('status', 'ACTIVE')->count();
        $totalActiveSaldo = (int) \App\Models\Student::whereNull('deleted_at')->where('status', 'ACTIVE')->sum('saldo');

        // Ambil daftar seluruh kelas beserta statistik santri aktif dan saldonya
        $classrooms = \App\Models\Classroom::with('school')
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

        // Cek jika ada santri aktif tanpa kelas
        $unassignedCount = \App\Models\Student::whereNull('classroom_id')->whereNull('deleted_at')->where('status', 'ACTIVE')->count();
        $unassignedSaldo = (int) \App\Models\Student::whereNull('classroom_id')->whereNull('deleted_at')->where('status', 'ACTIVE')->sum('saldo');
        $unassignedSaving = (int) \App\Models\Student::whereNull('classroom_id')->whereNull('deleted_at')->where('status', 'ACTIVE')->sum('saving');

        // Ambil ID kelas yang sudah benar-benar pernah dimigrasikan
        $migratedClassroomIds = [];
        if ($applicationSetting && is_array($applicationSetting->migrated_classrooms)) {
            $migratedClassroomIds = array_keys($applicationSetting->migrated_classrooms);
        }

        $historyMigratedIds = \App\Models\SaldoHistory::where('description', 'like', '%Penutupan Buku%')
            ->join('students', 'saldo_histories.student_id', '=', 'students.id')
            ->whereNotNull('students.classroom_id')
            ->pluck('students.classroom_id')
            ->unique()
            ->toArray();

        $migratedClassroomIds = array_values(array_unique(array_merge($migratedClassroomIds, $historyMigratedIds)));

        return view('admins.application-setting.index', compact(
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApplicationSettingRequest $request)
    {
        if (!Auth::user()->can('Edit Pengaturan Aplikasi')) {
            return redirect()->back()->with('error', 'Maaf, Anda tidak memiliki akses untuk halaman tersebut');
        }
        $data = $request->validated();

        // Handle login lock settings
        $data['is_login_locked'] = $request->boolean('is_login_locked');
        $data['allowed_roles_when_locked'] = $request->input('allowed_roles_when_locked', ['SUPER ADMIN', 'Bendahara SMP', 'BENDAHARA MA']);

        if ($request->hasFile('student_card_image')) {
            $data['student_card_image'] = $this->storeStudentCardImage($request->file('student_card_image'));
        }

        ApplicationSetting::updateOrCreate([], $data);

        return redirect()->route('application-setting.index')->with('success', 'Berhasil mengubah pengaturan aplikasi');
    }

    /**
     * DataTables endpoint untuk rincian data santri dan saldonya (bisa difilter per kelas).
     */
    public function studentData(Request $request)
    {
        $students = \App\Models\Student::with('classroom')
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

        return \Yajra\DataTables\Facades\DataTables::of($students)
            ->addIndexColumn()
            ->addColumn('classroom_name', fn($s) => $s->classroom?->name ?? 'Tanpa Kelas')
            ->editColumn('saldo', function ($s) {
                if ($s->saldo > 0) {
                    return '<span class="fw-bold text-success">Rp ' . number_format($s->saldo, 0, ',', '.') . '</span>';
                } elseif ($s->saldo < 0) {
                    return '<span class="fw-bold text-danger">Rp ' . number_format($s->saldo, 0, ',', '.') . '</span>';
                }
                return '<span class="text-muted fw-bold">Rp 0 <span class="badge badge-light-success fs-9 ms-1">Tutup Buku</span></span>';
            })
            ->editColumn('saving', fn($s) => 'Rp ' . number_format($s->saving, 0, ',', '.'))
            ->rawColumns(['saldo'])
            ->make(true);
    }

    /**
     * Endpoint data santri per kelas untuk Nested Table Expandable Panel (stay di satu fokus pandangan).
     */
    public function classroomStudents(Request $request)
    {
        $classroomId = $request->input('classroom_id');

        $query = \App\Models\Student::with('classroom')
            ->whereNull('deleted_at')
            ->where('status', 'ACTIVE')
            ->select('id', 'nis', 'name', 'classroom_id', 'saldo', 'saving');

        if ($classroomId === 'unassigned') {
            $query->whereNull('classroom_id');
            $className = 'Tanpa Kelas';
            $schoolName = '-';
        } else {
            $classroom = \App\Models\Classroom::with('school')->find($classroomId);
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

        return response()->json([
            'status' => 'success',
            'classroom_id' => $classroomId,
            'classroom_name' => $className,
            'school_name' => $schoolName,
            'is_closed' => $isClosed,
            'total_students' => $students->count(),
            'total_saldo' => (int) $students->sum('saldo'),
            'total_saving' => (int) $students->sum('saving'),
            'students' => $students->map(function ($s, $idx) use ($isClosed) {
                return [
                    'no' => $idx + 1,
                    'id' => (string) $s->id,
                    'nis' => (string) ($s->nis ?? '-'),
                    'name' => (string) $s->name,
                    'saldo' => (int) $s->saldo,
                    'saving' => (int) $s->saving,
                    'is_closed' => $isClosed || $s->saldo == 0,
                ];
            }),
        ]);
    }

    /**
     * Kirim data snapshot saldo per kelas ke Aplikasi Baru dan lakukan Tutup Buku (saldo di aplikasi lama menjadi 0).
     */
    public function sendMigration(Request $request)
    {
        if (!Auth::user()->can('Manage Pengaturan Aplikasi') && !Auth::user()->hasRole('SUPER ADMIN')) {
            return redirect()->back()->with('error', 'Maaf, Anda tidak memiliki izin untuk migrasi data.');
        }

        $request->validate([
            'classroom_id' => 'required|string',
        ]);

        $setting = ApplicationSetting::first();
        $targetUrl = rtrim($request->input('new_app_url', $setting?->new_app_url ?: 'https://sim.cahayatasbih.or.id'), '/');
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
            $studentsModel = \App\Models\Student::whereNull('classroom_id')
                ->whereNull('deleted_at')
                ->where('status', 'ACTIVE')
                ->select('id', 'nis', 'nisn', 'name', 'classroom_id', 'saldo', 'saving')
                ->get();
        } else {
            $classroom = \App\Models\Classroom::findOrFail($classroomId);
            $classroomName = $classroom->name;
            $studentsModel = \App\Models\Student::where('classroom_id', $classroom->id)
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
                \Illuminate\Support\Facades\DB::transaction(function () use ($studentsModel, $classroomName, $batchId) {
                    $now = now();
                    foreach ($studentsModel as $s) {
                        $currentSaldo = (int) $s->saldo;
                        if ($currentSaldo != 0) {
                            \App\Models\SaldoHistory::create([
                                'id' => (string) \Illuminate\Support\Str::uuid(),
                                'student_id' => $s->id,
                                'type' => $currentSaldo > 0 ? \App\Models\SaldoHistory::TYPE_OUT : \App\Models\SaldoHistory::TYPE_IN,
                                'amount' => abs($currentSaldo),
                                'description' => "Penutupan Buku: Migrasi Saldo ke SIM Baru (Kelas {$classroomName} - Ref Batch: {$batchId})",
                                'status' => \App\Models\SaldoHistory::STATUS_SUCCESS,
                                'usage' => \App\Models\SaldoHistory::USAGE_BILL,
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

                return redirect()->route('application-setting.index')->with('success', "✅ BERHASIL! Data Kelas {$classroomName} ({$totalStudents} santri, Total Saldo: Rp " . number_format($totalSaldo, 0, ',', '.') . ") telah dikirim ke Aplikasi Baru (Batch Ref: {$batchId}) dan saldo di aplikasi lama telah resmi DITUTUP BUKU (menjadi Rp 0). Seluruh riwayat transaksi tetap tersimpan utuh dan dapat dilihat di menu Laporan Saldo.");
            } else {
                $err = $response->json()['message'] ?? $response->body() ?? 'Server aplikasi baru menolak request';
                return redirect()->route('application-setting.index')->with('error', "Gagal mengirim data migrasi (HTTP {$response->status()}): " . \Illuminate\Support\Str::limit($err, 150));
            }
        } catch (\Throwable $e) {
            return redirect()->route('application-setting.index')->with('error', "Koneksi ke aplikasi baru gagal: " . $e->getMessage());
        }
    }

    /**
     * Failback / Migrasi Balik: Mengambil saldo berjalan real-time dari Aplikasi Baru
     * dan memulihkannya kembali ke Aplikasi Lama, lalu membuka kembali status kelas.
     */
    public function reverseMigration(Request $request)
    {
        $classroomId = $request->input('classroom_id');
        $setting = ApplicationSetting::first();
        $targetUrl = rtrim($request->input('new_app_url') ?: ($setting?->new_app_url ?: 'https://sim.cahayatasbih.or.id'), '/');
        $token = $request->input('migration_token') ?: ($setting?->migration_token ?: 'cahaya-tasbih-migration-secret');

        if ($classroomId === 'unassigned') {
            $classroomName = 'Tanpa Kelas';
            $studentsQuery = Student::whereNull('classroom_id');
        } else {
            $classroom = Classroom::find($classroomId);
            if (!$classroom) {
                return redirect()->route('application-setting.index')->with('error', 'Kelas tidak ditemukan.');
            }
            $classroomName = $classroom->name;
            $studentsQuery = Student::where('classroom_id', $classroomId);
        }

        $studentsModel = $studentsQuery->get();
        if ($studentsModel->isEmpty()) {
            return redirect()->route('application-setting.index')->with('error', "Tidak ada santri di kelas {$classroomName} untuk dipulihkan.");
        }

        $studentNises = $studentsModel->pluck('nis')->filter()->values()->toArray();

        try {
            $endpoint = $targetUrl . '/api/internal/migration/export-current-saldo';
            $response = Http::timeout(60)->post($endpoint, [
                'migration_token' => $token,
                'classroom_name' => $classroomName,
                'student_nises' => $studentNises,
            ]);

            if (!$response->successful()) {
                $err = $response->json()['message'] ?? $response->body() ?? 'Server Aplikasi Baru menolak permintaan';
                return redirect()->route('application-setting.index')->with('error', "Gagal menarik saldo dari Aplikasi Baru (HTTP {$response->status()}): " . \Illuminate\Support\Str::limit($err, 150));
            }

            $resData = $response->json();
            $newStudents = collect($resData['students'] ?? []);

            if ($newStudents->isEmpty()) {
                return redirect()->route('application-setting.index')->with('error', "Aplikasi Baru tidak menemukan data santri untuk kelas {$classroomName}.");
            }

            $studentsByKey = $studentsModel->keyBy(function ($item) {
                return !empty($item->nis) ? (string) $item->nis : (string) $item->name;
            });

            $restoredCount = 0;
            $totalSaldoRestored = 0;

            \Illuminate\Support\Facades\DB::transaction(function () use ($newStudents, $studentsByKey, $classroomName, &$restoredCount, &$totalSaldoRestored) {
                $now = now();
                foreach ($newStudents as $ns) {
                    $lookupKey = !empty($ns['nis']) ? (string) $ns['nis'] : (string) $ns['name'];
                    $student = $studentsByKey->get($lookupKey);

                    if ($student) {
                        $currentSaldo = (int) $student->saldo;
                        $realtimeSaldo = (int) ($ns['saldo'] ?? 0);
                        $realtimeSaving = isset($ns['saving']) ? (int) $ns['saving'] : (int) $student->saving;

                        $diff = $realtimeSaldo - $currentSaldo;

                        if ($diff != 0) {
                            \App\Models\SaldoHistory::create([
                                'id' => (string) \Illuminate\Support\Str::uuid(),
                                'student_id' => $student->id,
                                'type' => $diff > 0 ? \App\Models\SaldoHistory::TYPE_IN : \App\Models\SaldoHistory::TYPE_OUT,
                                'amount' => abs($diff),
                                'description' => "Penyesuaian Saldo: Failback / Penarikan Saldo Berjalan dari SIM Baru (Kelas {$classroomName})",
                                'status' => \App\Models\SaldoHistory::STATUS_SUCCESS,
                                'usage' => \App\Models\SaldoHistory::USAGE_BILL,
                                'balance_before' => $currentSaldo,
                                'balance_after' => $realtimeSaldo,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        $student->update([
                            'saldo' => $realtimeSaldo,
                            'saving' => $realtimeSaving,
                        ]);

                        $restoredCount++;
                        $totalSaldoRestored += $realtimeSaldo;
                    }
                }
            });

            // Buka kembali status kelas dari catatan migrated_classrooms
            if ($setting) {
                $currentMigrated = is_array($setting->migrated_classrooms) ? $setting->migrated_classrooms : [];
                unset($currentMigrated[$classroomId]);
                $setting->update([
                    'migrated_classrooms' => $currentMigrated,
                ]);
            }

            return redirect()->route('application-setting.index')->with('success', "✅ BERHASIL FAILBACK! Saldo berjalan real-time dari Aplikasi Baru untuk Kelas {$classroomName} ({$restoredCount} santri, Total Saldo: Rp " . number_format($totalSaldoRestored, 0, ',', '.') . ") telah ditarik dan dipulihkan ke aplikasi ini. Riwayat transaksi telah dicatat dan kelas dibuka kembali.");
        } catch (\Throwable $e) {
            return redirect()->route('application-setting.index')->with('error', "Koneksi ke Aplikasi Baru gagal saat tarik balik: " . $e->getMessage());
        }
    }

    private function storeStudentCardImage($file)
    {
        $imagePath = $file->store('images/student-card', 'public');

        $previousImage = ApplicationSetting::value('student_card_image');
        if ($previousImage && file_exists($previousImage)) {
            unlink($previousImage);
        }

        return 'storage/' . $imagePath;
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
