<?php

use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes([
    'verify'   => false,
    'register' => false,
    'reset'    => true
]);

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->prefix('/dashboard')->group(function () {
    // Student Dashboard Routes
    Route::middleware(['role:siswa'])->prefix('/student')->group(function () {
        Route::get('/', [StudentDashboardController::class, 'index'])->name('student.dashboard');
        Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('student.profile');
        Route::get('/attendance/history', [StudentDashboardController::class, 'attendanceHistory'])->name('student.attendance.history');
        Route::get('/attendance/data', [StudentDashboardController::class, 'getAttendanceData'])->name('student.attendance.data');
        Route::get('/qrcode', [StudentDashboardController::class, 'qrCode'])->name('student.qrcode');
        Route::get('/qrcode/{nis}/generate', [StudentDashboardController::class, 'generateQrCode'])->name('student.qrcode.generate')->where('nis', '[0-9A-Za-z]+');
        Route::get('/qrcode/{nis}/download', [StudentDashboardController::class, 'downloadQrCode'])->name('student.qrcode.download')->where('nis', '[0-9A-Za-z]+');
        Route::get('/qrcode/{nis}/print', [StudentDashboardController::class, 'printQrCode'])->name('student.qrcode.print')->where('nis', '[0-9A-Za-z]+');

        // Change Password Routes
        Route::get('/change-password', [StudentDashboardController::class, 'changePassword'])->name('student.change-password');
        Route::put('/change-password', [StudentDashboardController::class, 'updatePassword'])->name('student.update-password');
    });

    Route::middleware(['role:admin,guru'])->group(function () {
        Route::get('/', [DashboardController::class, 'viewDashboard'])->name('dashboard');
        // Dashboard AJAX endpoints
        Route::get('/attendance-data', [DashboardController::class, 'getAttendanceData'])->name('dashboard.attendance-data');
        Route::get('/statistics', [DashboardController::class, 'getStatistics'])->name('dashboard.statistics');
        Route::get('/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');

        // Change Password Routes for Guru
        Route::get('/change-password', [GuruController::class, 'changePassword'])->name('guru.change-password');
        Route::put('/change-password', [GuruController::class, 'updatePassword'])->name('guru.update-password');

        // Attendance Management Routes
        Route::prefix('/attendance')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('/create-session', [AttendanceController::class, 'createSession'])->name('attendance.create-session');
            Route::post('/end-session', [AttendanceController::class, 'endSession'])->name('attendance.end-session');
            Route::get('/qr-scan', [AttendanceController::class, 'qrScan'])->name('attendance.qr-scan');
            Route::post('/process-qr', [AttendanceController::class, 'processQrScan'])->name('attendance.process-qr');
            Route::get('/manual', [AttendanceController::class, 'manualAttendance'])->name('attendance.manual');
            Route::post('/manual', [AttendanceController::class, 'storeManualAttendance'])->name('attendance.store-manual');
            Route::get('/data', [AttendanceController::class, 'getAttendanceData'])->name('attendance.data');
            Route::get('/statistics-by-class', [AttendanceController::class, 'getStatisticsByClass'])->name('attendance.statistics-by-class');
            Route::get('/download-surat/{id}', [AttendanceController::class, 'downloadSuratIzin'])->name('attendance.download-surat');

            // Attendance Reports Routes
            Route::prefix('/reports')->group(function () {
                Route::get('/', [AttendanceReportController::class, 'index'])->name('attendance.reports.index');
                Route::get('/daily', [AttendanceReportController::class, 'dailyReport'])->name('attendance.reports.daily');
                Route::get('/daily/data', [AttendanceReportController::class, 'getDailyReportData'])->name('attendance.reports.daily.data');
                Route::get('/daily/export-pdf', [AttendanceReportController::class, 'exportDailyReportPdf'])->name('attendance.reports.daily.export-pdf');
                Route::get('/monthly', [AttendanceReportController::class, 'monthlyReport'])->name('attendance.reports.monthly');
                Route::get('/monthly/data', [AttendanceReportController::class, 'getMonthlyReportData'])->name('attendance.reports.monthly.data');
                Route::get('/monthly/chart', [AttendanceReportController::class, 'getMonthlyChartData'])->name('attendance.reports.monthly.chart');
                Route::get('/monthly/export-pdf', [AttendanceReportController::class, 'exportMonthlyReportPdf'])->name('attendance.reports.monthly.export-pdf');
                Route::get('/yearly', [AttendanceReportController::class, 'yearlyReport'])->name('attendance.reports.yearly');
                Route::get('/yearly/data', [AttendanceReportController::class, 'getYearlyReportData'])->name('attendance.reports.yearly.data');
                Route::get('/yearly/chart', [AttendanceReportController::class, 'getYearlyChartData'])->name('attendance.reports.yearly.chart');
                Route::get('/yearly/export-pdf', [AttendanceReportController::class, 'exportYearlyReportPdf'])->name('attendance.reports.yearly.export-pdf');
                Route::get('/student', [AttendanceReportController::class, 'studentReport'])->name('attendance.reports.student');
                Route::get('/student/search', [AttendanceReportController::class, 'getStudentSearchData'])->name('attendance.reports.student.search');
                Route::get('/student/export-pdf', [AttendanceReportController::class, 'exportStudentReportPdf'])->name('attendance.reports.student.export-pdf');
                Route::get('/class', [AttendanceReportController::class, 'classReport'])->name('attendance.reports.class');
                Route::get('/class/data', [AttendanceReportController::class, 'getClassReportData'])->name('attendance.reports.class.data');
                Route::get('/class/export-pdf', [AttendanceReportController::class, 'exportClassReportPdf'])->name('attendance.reports.class.export-pdf');
            });
        });

        // Siswa Management Routes
        Route::prefix('/siswa')->group(function () {
            // Main views
            Route::get('/', [SiswaController::class, 'viewSiswa'])->name('siswa.index');
            Route::get('/add', [SiswaController::class, 'viewAddSiswa'])->name('siswa.add');
            Route::post('/add', [SiswaController::class, 'addSiswa'])->name('siswa.store');
            Route::get('/{id}/detail', [SiswaController::class, 'viewDetailSiswa'])->name('siswa.show');
            Route::get('/{id}/edit', [SiswaController::class, 'viewEditSiswa'])->name('siswa.edit');
            Route::put('/{id}/edit', [SiswaController::class, 'updateSiswa'])->name('siswa.update');
            Route::delete('/{id}/delete', [SiswaController::class, 'deleteSiswa'])->name('siswa.destroy');

            // AJAX Data endpoints
            Route::get('/data', [SiswaController::class, 'getSiswaData'])->name('siswa.data');
            Route::get('/statistics', [SiswaController::class, 'getStatistics'])->name('siswa.statistics');

            // Import/Export
            Route::get('/template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');
            Route::post('/import', [SiswaController::class, 'importSiswa'])->name('siswa.import');

            // Card
            Route::get('/{id}/card', [SiswaController::class, 'generateStudentCard'])->name('siswa.card');
            Route::get('/{id}/print-card', [SiswaController::class, 'printStudentCard'])->name('siswa.print-card');
            Route::get('/{id}/download-card', [SiswaController::class, 'downloadStudentCard'])->name('siswa.download-card');
            Route::get('/{id}/qrcode', [SiswaController::class, 'getQRCode'])->name('siswa.qrcode');
            Route::get('/{id}/card-data', [SiswaController::class, 'getCardData'])->name('siswa.card-data');

            // Show by Class
            Route::get('/class/{kelasId}', [SiswaController::class, 'showByClass'])->name('siswa.by-class');
            Route::get('/class/{kelasId}/data', [SiswaController::class, 'getSiswaDataByClass'])->name('siswa.by-class.data');
            Route::delete('/class/{kelasId}/delete-all', [SiswaController::class, 'deleteAllStudentsInClass'])->name('siswa.by-class.delete-all');

            // Bulk Card Generation
            Route::get('/cards/class/{kelasId}', [SiswaController::class, 'generateCardsByClass'])->name('siswa.cards.class');
            Route::get('/cards/class/{kelasId}/print', [SiswaController::class, 'printCardsByClass'])->name('siswa.cards.class.print');
            Route::get('/available-classes', [SiswaController::class, 'getAvailableClasses'])->name('siswa.available-classes');
        });

        Route::middleware(['role:admin'])->group(function () {
            // Kelas Management Routes
            Route::prefix('/kelas')->group(function () {
                Route::get('/', [KelasController::class, 'viewKelas'])->name('kelas.index');
                Route::get('/add', [KelasController::class, 'viewAddKelas'])->name('kelas.add');
                Route::post('/store-kelas', [KelasController::class, 'storeKelas'])->name('kelas.store.kelas');
                Route::get('/{id}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
                Route::put('/{id}/edit', [KelasController::class, 'update'])->name('kelas.update');
                Route::delete('/{id}/delete', [KelasController::class, 'destroy'])->name('kelas.destroy');

                // AJAX Data endpoints
                Route::get('/data', [KelasController::class, 'getKelasData'])->name('kelas.data');
                Route::get('/available-jurusan', [KelasController::class, 'getAvailableJurusan'])->name('kelas.available.jurusan');
                Route::get('/statistics', [KelasController::class, 'getStatistics'])->name('kelas.statistics');

                // Store operations (AJAX)
                Route::post('/tingkat-kelas', [KelasController::class, 'storeTingkatKelas'])->name('kelas.store.tingkat');
                Route::post('/jurusan', [KelasController::class, 'storeJurusan'])->name('kelas.store.jurusan');

                // Update operations (AJAX)
                Route::put('/jurusan/{id}', [KelasController::class, 'updateJurusan'])->name('kelas.jurusan.update');

                // Delete operations (AJAX)
                Route::delete('/tingkat-kelas/{id}', [KelasController::class, 'destroyTingkatKelas'])->name('kelas.tingkat.destroy');
                Route::delete('/jurusan/{id}', [KelasController::class, 'destroyJurusan'])->name('kelas.jurusan.destroy');
            });

            // Guru Management Routes
            Route::prefix('/guru')->group(function () {
                Route::get('/', [GuruController::class, 'index'])->name('guru.index');
                Route::get('/create', [GuruController::class, 'create'])->name('guru.create');
                Route::post('/create', [GuruController::class, 'store'])->name('guru.store');
                Route::get('/{id}/edit', [GuruController::class, 'edit'])->name('guru.edit');
                Route::put('/{id}/edit', [GuruController::class, 'update'])->name('guru.update');
                Route::delete('/{id}/delete', [GuruController::class, 'destroy'])->name('guru.destroy');

                // AJAX Data endpoints
                Route::get('/data', [GuruController::class, 'getGuruData'])->name('guru.data');
                Route::get('/statistics', [GuruController::class, 'getStatistics'])->name('guru.statistics');
            });

            // App Settings Routes
            Route::prefix('/settings')->group(function () {
                Route::get('/', [AppSettingController::class, 'index'])->name('settings.index');
                Route::get('/edit', [AppSettingController::class, 'edit'])->name('settings.edit');
                Route::put('/update', [AppSettingController::class, 'update'])->name('settings.update');
            });
        });
    });
});
