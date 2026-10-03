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

        return view('admins.application-setting.index', compact('applicationSetting', 'roles', 'totalActiveStudents', 'totalActiveSaldo'));
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
     * Kirim data snapshot saldo seluruh santri aktif ke Aplikasi Baru untuk ditinjau dan dikonfirmasi.
     */
    public function sendMigration(Request $request)
    {
        if (!Auth::user()->can('Manage Pengaturan Aplikasi') && !Auth::user()->hasRole('SUPER ADMIN')) {
            return redirect()->back()->with('error', 'Maaf, Anda tidak memiliki izin untuk migrasi data.');
        }

        $setting = ApplicationSetting::first();
        $targetUrl = rtrim($request->input('new_app_url', $setting?->new_app_url ?: 'https://sim.cahayatasbih.or.id'), '/');
        $token = $request->input('migration_token', $setting?->migration_token ?: 'cahaya-tasbih-migration-secret');

        if ($setting) {
            $setting->update([
                'new_app_url' => $targetUrl,
                'migration_token' => $token,
            ]);
        }

        $students = \App\Models\Student::with('classroom')
            ->whereNull('deleted_at')
            ->where('status', 'ACTIVE')
            ->select('id', 'nis', 'nisn', 'name', 'classroom_id', 'saldo', 'saving')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => (string) $s->id,
                    'nis' => (string) ($s->nis ?? ''),
                    'nisn' => (string) ($s->nisn ?? ''),
                    'name' => (string) $s->name,
                    'classroom' => (string) ($s->classroom?->name ?? '-'),
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
            'total_students' => $totalStudents,
            'total_saldo' => $totalSaldo,
            'total_saving' => $totalSaving,
            'students' => $students,
        ];

        try {
            $endpoint = $targetUrl . '/api/internal/migration/receive-saldo';
            $response = Http::timeout(60)->post($endpoint, $payload);

            if ($response->successful()) {
                if ($setting) {
                    $setting->update(['last_migration_sent_at' => now()]);
                }
                $resData = $response->json();
                $batchId = $resData['batch_id'] ?? '-';
                return redirect()->route('application-setting.index')->with('success', "Data migrasi {$totalStudents} santri (Total Saldo: Rp " . number_format($totalSaldo, 0, ',', '.') . ") berhasil dikirim ke Aplikasi Baru! (Batch ID: {$batchId}). Silakan buka Aplikasi Baru untuk meninjau dan mengonfirmasi penerimaan data.");
            } else {
                $err = $response->json()['message'] ?? $response->body() ?? 'Server aplikasi baru menolak request';
                return redirect()->route('application-setting.index')->with('error', "Gagal mengirim data migrasi (HTTP {$response->status()}): " . \Illuminate\Support\Str::limit($err, 150));
            }
        } catch (\Throwable $e) {
            return redirect()->route('application-setting.index')->with('error', "Koneksi ke aplikasi baru gagal: " . $e->getMessage());
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
