<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Exports\SiswaTemplateExport;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    /**
     * Display index page with DataTable
     */
    public function viewSiswa()
    {
        return view('dashboard.siswa.index');
    }

    /**
     * Get data for DataTables AJAX
     */
    public function getSiswaData(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $query = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])
            ->select(['id', 'user_id', 'kelas_id', 'nis', 'jenis_kelamin', 'tanggal_lahir', 'no_telepon', 'updated_at', 'created_at'])
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                return $row->user->name ?? '-';
            })
            ->addColumn('kelas', function ($row) {
                if ($row->kelas) {
                    return '<a href="' . route('siswa.by-class', $row->kelas->id) . '" class="text-decoration-none fw-medium text-primary" title="Lihat semua siswa di kelas ' . $row->kelas->nama_kelas . '">' . $row->kelas->nama_kelas . '</a>';
                }
                return '<span class="text-muted">Belum ada kelas</span>';
            })
            ->addColumn('umur', function ($row) {
                return $row->tanggal_lahir ? Carbon::parse($row->tanggal_lahir)->age . ' tahun' : '-';
            })
            ->editColumn('jenis_kelamin', function ($row) {
                $badge = $row->jenis_kelamin === 'L' ? 'bg-primary' : 'bg-info';
                $text = $row->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
                return '<span class="badge ' . $badge . '">' . $text . '</span>';
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : null;
            })
            ->addColumn('action', function ($row) {
                return '
                <div class="btn-group" role="group">
                    <a href="' . route('siswa.show', $row->id) . '" class="btn btn-sm btn-outline-info"
                            title="Lihat Detail">
                        <i class="ri-eye-line"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-primary edit-btn"
                            data-id="' . $row->id . '"
                            type="button"
                            title="Edit Siswa">
                        <i class="ri-edit-line"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-btn"
                            data-id="' . $row->id . '"
                            type="button"
                            title="Hapus Siswa">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>';
            })
            ->filterColumn('nama', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('kelas', function ($query, $keyword) {
                $query->whereHas('kelas', function ($q) use ($keyword) {
                    $q->where('nama_kelas', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['kelas', 'jenis_kelamin', 'action'])
            ->make(true);
    }

    /**
     * Show create form
     */
    public function viewAddSiswa()
    {
        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->orderBy('nama_kelas')
            ->get();

        return view('dashboard.siswa.add', compact('kelasList'));
    }

    /**
     * Store siswa
     */
    public function addSiswa(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nis' => 'required|string|max:20|unique:siswas,nis',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'tanggal_masuk' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Nama siswa harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'nis.required' => 'NIS harus diisi',
            'nis.unique' => 'NIS sudah terdaftar',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'alamat.required' => 'Alamat harus diisi',
            'no_telepon.required' => 'Nomor telepon harus diisi',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'kelas_id.required' => 'Kelas harus dipilih',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus jpeg, png, jpg, atau gif',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        try {
            DB::beginTransaction();

            // Handle foto upload
            $fotoName = 'avatar.png'; // default
            if ($request->hasFile('foto')) {
                $fotoName = $request->file('foto')->store('siswa', 'public');
            }

            // Create user account
            $user = User::create([
                'name' => trim($request->name),
                'email' => trim($request->email),
                'password' => Hash::make('123'), // Default password
                'role' => 'siswa',
                'email_verified_at' => now()
            ]);

            // Create siswa profile
            $siswa = Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $request->kelas_id,
                'nis' => trim($request->nis),
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => trim($request->alamat),
                'no_telepon' => trim($request->no_telepon),
                'tanggal_masuk' => $request->tanggal_masuk,
                'foto' => $fotoName,
            ]);

            DB::commit();

            return redirect()->route('siswa.index')
                ->with('success', "Siswa {$user->name} berhasil ditambahkan.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan siswa.');
        }
    }

    /**
     * Show detail siswa
     */
    public function viewDetailSiswa($id)
    {
        try {
            $siswa = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])->findOrFail($id);

            // Get app settings for school info
            $appSettings = AppSettings::first();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $siswa
                ]);
            }

            return view('dashboard.siswa.show', compact('siswa', 'appSettings'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan'
                ], 404);
            }

            return redirect()->route('siswa.index')
                ->with('error', 'Data siswa tidak ditemukan.');
        }
    }

    /**
     * Show edit form
     */
    public function viewEditSiswa($id)
    {
        try {
            $siswa = Siswa::with(['user', 'kelas'])->findOrFail($id);
            $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
                ->orderBy('nama_kelas')
                ->get();

            return view('dashboard.siswa.edit', compact('siswa', 'kelasList'));
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')
                ->with('error', 'Data siswa tidak ditemukan.');
        }
    }

    /**
     * Update siswa
     */
    public function updateSiswa(Request $request, $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($siswa->user_id)],
            'nis' => ['required', 'string', 'max:20', Rule::unique('siswas')->ignore($id)],
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'tanggal_masuk' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Nama siswa harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'nis.required' => 'NIS harus diisi',
            'nis.unique' => 'NIS sudah terdaftar',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'alamat.required' => 'Alamat harus diisi',
            'no_telepon.required' => 'Nomor telepon harus diisi',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'kelas_id.required' => 'Kelas harus dipilih',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus jpeg, png, jpg, atau gif',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        try {
            DB::beginTransaction();

            // Handle foto upload
            $fotoName = $siswa->foto;
            if ($request->hasFile('foto')) {
                if ($siswa->foto && $siswa->foto !== 'avatar.png' && Storage::exists('public/' . $siswa->foto)) {
                    Storage::delete('public/' . $siswa->foto);
                }
                $fotoName = $request->file('foto')->store('siswa', 'public');
            }

            // Update user account
            $siswa->user->update([
                'name' => trim($request->name),
                'email' => trim($request->email),
            ]);

            // Update siswa profile
            $siswa->update([
                'kelas_id' => $request->kelas_id,
                'nis' => trim($request->nis),
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => trim($request->alamat),
                'no_telepon' => trim($request->no_telepon),
                'tanggal_masuk' => $request->tanggal_masuk,
                'foto' => $fotoName,
            ]);

            DB::commit();

            return redirect()->route('siswa.index')
                ->with('success', "Data siswa {$siswa->user->name} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data siswa.');
        }
    }

    /**
     * Delete siswa
     */
    public function deleteSiswa($id)
    {
        try {
            DB::beginTransaction();

            $siswa = Siswa::with('user')->findOrFail($id);
            $namaSiswa = $siswa->user->name;
            // Delete foto if not default
            if ($siswa->foto && $siswa->foto !== 'avatar.png' && Storage::exists('public/' . $siswa->foto)) {
                Storage::delete('public/' . $siswa->foto);
            }
            $siswa->delete();

            // Then delete user account
            $siswa->user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Siswa {$namaSiswa} berhasil dihapus."
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus siswa.'
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $totalSiswa = Siswa::count();
            $siswaLaki = Siswa::where('jenis_kelamin', 'L')->count();
            $siswaPerempuan = Siswa::where('jenis_kelamin', 'P')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_siswa' => $totalSiswa,
                    'siswa_laki' => $siswaLaki,
                    'siswa_perempuan' => $siswaPerempuan,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik'
            ], 500);
        }
    }

    /**
     * Generate printable student card view
     */
    public function generateStudentCard($id)
    {
        try {
            $siswa = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])->findOrFail($id);
            $appSettings = AppSettings::first();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'siswa' => $siswa,
                        'app_settings' => $appSettings
                    ]
                ]);
            }

            return view('dashboard.siswa.card-print', compact('siswa', 'appSettings'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengambil data siswa'
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil data siswa');
        }
    }


    /**
     * Get QR Code for student
     */
    public function getQRCode($id)
    {
        try {
            $siswa = Siswa::findOrFail($id);

            // Generate QR code dengan parameter tetap untuk konsistensi
            $qrCode = \DNS2D::getBarcodePNG($siswa->nis, 'QRCODE', 3, 3);

            return response(base64_decode($qrCode), 200)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=86400'); // Cache 1 hari
        } catch (\Exception $e) {
            return response('', 404);
        }
    }

    /**
     * Download student card as Image (exact copy of show view)
     */
    public function downloadStudentCard($id)
    {
        try {
            $siswa = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])->findOrFail($id);
            $appSettings = AppSettings::first();

            return view('dashboard.siswa.card-download', compact('siswa', 'appSettings'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh kartu: ' . $e->getMessage());
        }
    }

    /**
     * Get card data for AJAX requests
     */
    public function getCardData($id)
    {
        try {
            $siswa = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])->findOrFail($id);
            $appSettings = AppSettings::first();

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'name' => $siswa->user->name,
                        'nis' => $siswa->nis,
                        'class' => $siswa->kelas ? $siswa->kelas->nama_kelas : 'Belum ada kelas',
                        'gender' => $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                        'birth_date' => $siswa->tanggal_lahir->format('d/m/Y'),
                        'entry_year' => $siswa->tanggal_masuk->format('Y'),
                        'photo' => $siswa->foto && $siswa->foto != 'avatar.png'
                            ? asset('storage/siswa/' . $siswa->foto)
                            : asset('avatar.png')
                    ],
                    'school' => [
                        'name' => $appSettings->nama_sekolah ?? 'SMK Negeri 1 Contoh',
                        'logo' => asset('logo.png')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data'
            ], 500);
        }
    }

    /**
     * Print student card view
     */
    public function printStudentCard($id)
    {
        try {
            $siswa = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])->findOrFail($id);
            $appSettings = AppSettings::first();

            return view('dashboard.siswa.card-print', compact('siswa', 'appSettings'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menampilkan halaman cetak kartu');
        }
    }

    /**
     * Preview student card in popup/modal
     */
    // public function previewStudentCard($id)
    // {
    //     try {
    //         $siswa = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])->findOrFail($id);
    //         $appSettings = AppSettings::first();

    //         return view('dashboard.siswa.card-preview', compact('siswa', 'appSettings'));
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat menampilkan preview kartu');
    //     }
    // }

    /**
     * Download template excel untuk import siswa
     */
    public function downloadTemplate()
    {
        try {
            // Create simple template without complex styling
            $headers = [
                ['Nama', 'Email', 'NIS', 'Kelas', 'Tanggal Lahir', 'Jenis Kelamin', 'Alamat', 'No Telepon', 'Tanggal Masuk']
            ];

            // Get first two classes for sample data
            $firstKelas = Kelas::first();
            $secondKelas = Kelas::skip(1)->first();

            // Generate unique emails and NIS with simpler format
            $timestamp = time();
            $sampleData = [
                ['John Doe', "john.doe{$timestamp}@student.sch.id", "{$timestamp}001", $firstKelas ? $firstKelas->nama_kelas : 'X IPA', '15/08/2005', 'L', 'Jl. Merdeka No. 123, Jakarta', "'081234567890", '15/07/2023'],
                ['Jane Smith', "jane.smith{$timestamp}@student.sch.id", "{$timestamp}002", $secondKelas ? $secondKelas->nama_kelas : 'X IPS', '22/03/2005', 'P', 'Jl. Sudirman No. 456, Jakarta', "'081987654321", '15/07/2023']
            ];

            // Get available classes
            $kelasList = Kelas::orderBy('nama_kelas')->get();
            $instructions = [
                [''],
                ['PETUNJUK PENGISIAN:'],
                ['1. Nama: Isi dengan nama lengkap siswa'],
                ['2. Email: Isi dengan alamat email yang unik (contoh: nama@student.sch.id)'],
                ['3. NIS: Isi dengan Nomor Induk Siswa yang unik (awali dengan tanda petik \' agar dianggap text)'],
                ['4. Kelas: Isi dengan nama kelas yang sudah ada di sistem (lihat daftar di bawah)'],
                ['5. Tanggal Lahir: Format DD/MM/YYYY (contoh: 15/08/2005)'],
                ['6. Jenis Kelamin: Isi dengan L (Laki-laki) atau P (Perempuan)'],
                ['7. Alamat: Isi dengan alamat lengkap siswa'],
                ['8. No Telepon: Isi dengan nomor telepon yang aktif (awali dengan \' agar dianggap text)'],
                ['9. Tanggal Masuk: Format DD/MM/YYYY (contoh: 15/07/2023)'],
                [''],
                ['CATATAN PENTING:'],
                ['- Untuk NIS dan No Telepon, awali dengan tanda petik (\') agar Excel tidak mengubah ke angka'],
                ['- Email harus unik dan belum terdaftar di sistem'],
                ['- NIS harus unik untuk setiap siswa'],
                ['- Pastikan nama kelas sesuai dengan daftar yang tersedia'],
                [''],
                ['DAFTAR KELAS YANG TERSEDIA:']
            ];

            foreach ($kelasList as $kelas) {
                $instructions[] = ["- {$kelas->nama_kelas}"];
            }

            $allData = array_merge($headers, $sampleData, $instructions);

            $export = new class($allData) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function array(): array
                {
                    return $this->data;
                }
            };

            return Excel::download($export, 'template_import_siswa.xlsx');
        } catch (\Exception $e) {
            Log::error('Template download error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }

    /**
     * Import data siswa dari excel
     */
    public function importSiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file.required' => 'File excel harus dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV',
            'file.max' => 'Ukuran file maksimal 2MB'
        ]);

        try {
            DB::beginTransaction();

            $import = new SiswaImport();
            Excel::import($import, $request->file('file'));

            $results = $import->getResults();

            DB::commit();

            // Get detailed error information
            $failures = $import->failures();
            $errors = $import->errors();

            $message = "Import selesai! ";
            $message .= "Berhasil: {$results['success']} data, ";
            $message .= "Gagal: {$results['failed']} data";

            if ($results['failed'] > 0) {
                $message .= "\n\nDetail Error:";

                // Add validation failures
                foreach ($failures as $failure) {
                    $message .= "\nBaris {$failure->row()}: ";
                    foreach ($failure->errors() as $error) {
                        $message .= $error . "; ";
                    }
                }

                // Add general errors
                foreach ($errors as $error) {
                    $message .= "\nError: " . $error;
                }
            }

            if ($results['success'] > 0 && $results['failed'] > 0) {
                return redirect()->route('siswa.index')->with('warning', $message);
            } elseif ($results['failed'] > 0) {
                return redirect()->route('siswa.index')->with('error', $message);
            } else {
                return redirect()->route('siswa.index')->with('success', $message);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Generate student cards by class
     */
    public function generateCardsByClass($kelasId)
    {
        try {
            $kelas = Kelas::with(['tingkatKelas', 'jurusan'])->findOrFail($kelasId);
            $siswaList = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])
                ->where('kelas_id', $kelasId)
                ->orderBy('nis')
                ->get();

            if ($siswaList->isEmpty()) {
                return redirect()->back()->with('error', "Tidak ada siswa di kelas {$kelas->nama_kelas}");
            }

            $appSettings = AppSettings::first();

            return view('dashboard.siswa.cards-by-class', compact('siswaList', 'kelas', 'appSettings'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat generate kartu: ' . $e->getMessage());
        }
    }

    /**
     * Get available classes for card generation
     */
    public function getAvailableClasses()
    {
        try {
            $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
                ->withCount('siswas')
                ->having('siswas_count', '>', 0)
                ->orderBy('nama_kelas')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kelasList
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kelas'
            ], 500);
        }
    }

    /**
     * Print student cards by class
     */
    public function printCardsByClass($kelasId)
    {
        try {
            $kelas = Kelas::with(['tingkatKelas', 'jurusan'])->findOrFail($kelasId);
            $siswaList = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])
                ->where('kelas_id', $kelasId)
                ->orderBy('nis')
                ->get();

            if ($siswaList->isEmpty()) {
                return redirect()->back()->with('error', "Tidak ada siswa di kelas {$kelas->nama_kelas}");
            }

            $appSettings = AppSettings::first();

            return view('dashboard.siswa.cards-by-class-print', compact('siswaList', 'kelas', 'appSettings'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat generate kartu untuk cetak: ' . $e->getMessage());
        }
    }

    /**
     * Show students by class
     */
    public function showByClass($kelasId)
    {
        try {
            $kelas = Kelas::with(['tingkatKelas', 'jurusan'])->findOrFail($kelasId);

            return view('dashboard.siswa.by-class', compact('kelas'));
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')->with('error', 'Kelas tidak ditemukan.');
        }
    }

    /**
     * Get students data by class for DataTables AJAX
     */
    public function getSiswaDataByClass(Request $request, $kelasId)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        try {
            $kelas = Kelas::findOrFail($kelasId);

            $query = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])
                ->where('kelas_id', $kelasId)
                ->select(['id', 'user_id', 'kelas_id', 'nis', 'jenis_kelamin', 'tanggal_lahir', 'no_telepon', 'updated_at', 'created_at'])
                ->orderBy('nis', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    return $row->user->name ?? '-';
                })
                ->addColumn('kelas', function ($row) {
                    return $row->kelas ? $row->kelas->nama_kelas : 'Belum ada kelas';
                })
                ->addColumn('umur', function ($row) {
                    return $row->tanggal_lahir ? Carbon::parse($row->tanggal_lahir)->age . ' tahun' : '-';
                })
                ->editColumn('jenis_kelamin', function ($row) {
                    $badge = $row->jenis_kelamin === 'L' ? 'bg-primary' : 'bg-info';
                    $text = $row->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
                    return '<span class="badge ' . $badge . '">' . $text . '</span>';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : null;
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="btn-group" role="group">
                        <a href="' . route('siswa.show', $row->id) . '" class="btn btn-sm btn-outline-info"
                                title="Lihat Detail">
                            <i class="ri-eye-line"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-primary edit-btn"
                                data-id="' . $row->id . '"
                                type="button"
                                title="Edit Siswa">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-btn"
                                data-id="' . $row->id . '"
                                type="button"
                                title="Hapus Siswa">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>';
                })
                ->filterColumn('nama', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['jenis_kelamin', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data'], 500);
        }
    }

    /**
     * Delete all students in a class
     */
    public function deleteAllStudentsInClass($kelasId)
    {
        try {
            DB::beginTransaction();

            $kelas = Kelas::findOrFail($kelasId);

            // Get all students in this class
            $students = Siswa::where('kelas_id', $kelasId)->get();

            if ($students->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada siswa di kelas ' . $kelas->nama_kelas
                ], 404);
            }

            $totalDeleted = 0;
            $userIds = [];

            // Delete each student and collect user IDs
            foreach ($students as $siswa) {
                if ($siswa->user_id) {
                    $userIds[] = $siswa->user_id;
                }
                $siswa->delete();
                $totalDeleted++;
            }

            // Delete all associated users
            if (!empty($userIds)) {
                User::whereIn('id', $userIds)->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$totalDeleted} siswa dari kelas {$kelas->nama_kelas}",
                'total_deleted' => $totalDeleted
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting all students in class: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus siswa: ' . $e->getMessage()
            ], 500);
        }
    }
}
