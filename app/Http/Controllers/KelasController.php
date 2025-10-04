<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Kelas;
use App\Models\TingkatKelas;
use App\Models\Jurusan;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    /**
     * Display index page with DataTable
     */
    public function viewKelas()
    {
        return view('dashboard.kelas.index');
    }

    /**
     * Get data for DataTables AJAX
     */
    public function getKelasData(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $query = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_kelas_link', function ($row) {
                return '<a href="' . route('siswa.by-class', $row->id) . '" class="text-decoration-none fw-medium text-primary" title="Lihat daftar siswa kelas ' . $row->nama_kelas . '">' . $row->nama_kelas . '</a>';
            })
            ->addColumn('jumlah_siswa', function ($row) {
                $count = $row->siswas_count;
                $badgeClass = $count > 0 ? 'bg-success' : 'bg-secondary';
                return '<a href="' . route('siswa.by-class', $row->id) . '" class="badge ' . $badgeClass . ' text-decoration-none" title="Lihat daftar siswa">' . $count . ' Siswa</a>';
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : null;
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group" role="group">
                        <a href="' . route('siswa.by-class', $row->id) . '" class="btn btn-sm btn-outline-info"
                                title="Lihat Siswa">
                            <i class="ri-group-line"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-primary edit-btn"
                                data-id="' . $row->id . '"
                                type="button"
                                title="Edit Kelas">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-btn"
                                data-id="' . $row->id . '"
                                type="button"
                                title="Hapus Kelas">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>';
            })
            ->filterColumn('kelas', function ($query, $keyword) {
                $query->whereHas('tingkatKelas', function ($q) use ($keyword) {
                    $q->where('tingkat', 'like', "%{$keyword}%");
                })->orWhereHas('jurusan', function ($q) use ($keyword) {
                    $q->where('kode_jurusan', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['nama_kelas_link', 'jumlah_siswa', 'action'])
            ->make(true);
    }

    /**
     * Show create form with 3 tabs
     */
    public function viewAddKelas()
    {
        $tingkatKelas = TingkatKelas::orderBy('tingkat')->get();
        $jurusans = Jurusan::orderBy('kode_jurusan')->get();
        $existingKelas = Kelas::with(['tingkatKelas', 'jurusan'])->get();

        return view('dashboard.kelas.add', compact('tingkatKelas', 'jurusans', 'existingKelas'));
    }

    /**
     * Store tingkat kelas via AJAX
     */
    public function storeTingkatKelas(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $request->validate([
            'tingkat' => 'required|string|max:10|unique:tingkat_kelas,tingkat'
        ], [
            'tingkat.required' => 'Tingkat harus diisi',
            'tingkat.unique' => 'Tingkat sudah ada',
            'tingkat.max' => 'Tingkat maksimal 10 karakter'
        ]);

        try {
            DB::beginTransaction();

            $tingkatKelas = TingkatKelas::create([
                'tingkat' => strtoupper(trim($request->tingkat))
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tingkat kelas berhasil ditambahkan',
                'data' => $tingkatKelas
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan tingkat kelas'
            ], 500);
        }
    }

    /**
     * Store jurusan via AJAX
     */
    public function storeJurusan(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusans,kode_jurusan',
            'nama_jurusan' => 'required|string|max:100'
        ], [
            'kode_jurusan.required' => 'Kode jurusan harus diisi',
            'kode_jurusan.unique' => 'Kode jurusan sudah ada',
            'kode_jurusan.max' => 'Kode jurusan maksimal 20 karakter',
            'nama_jurusan.required' => 'Nama jurusan harus diisi',
            'nama_jurusan.max' => 'Nama jurusan maksimal 100 karakter'
        ]);

        try {
            DB::beginTransaction();

            $jurusan = Jurusan::create([
                'kode_jurusan' => strtoupper(trim($request->kode_jurusan)),
                'nama_jurusan' => trim($request->nama_jurusan)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jurusan berhasil ditambahkan',
                'data' => $jurusan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan jurusan'
            ], 500);
        }
    }

    /**
     * Store kelas via AJAX
     */
    public function storeKelas(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $request->validate([
            'tingkat_kelas_id' => 'required|string',
            'jurusan_id' => 'required|string',
            'wali_kelas' => 'required|string|max:255',
        ], [
            'tingkat_kelas_id.required' => 'Tingkat kelas harus dipilih',
            'jurusan_id.required' => 'Jurusan harus dipilih',
            'wali_kelas.required' => 'Nama wali kelas harus diisi',
            'wali_kelas.max' => 'Nama wali kelas maksimal 255 karakter',
        ]);

        try {
            DB::beginTransaction();

            // Cari tingkat kelas berdasarkan tingkat string
            $tingkatKelas = TingkatKelas::where('tingkat', $request->tingkat_kelas_id)->first();
            if (!$tingkatKelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tingkat kelas tidak ditemukan'
                ], 422);
            }

            // Cari jurusan berdasarkan kode
            $jurusan = Jurusan::where('kode_jurusan', $request->jurusan_id)->first();
            if (!$jurusan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurusan tidak ditemukan'
                ], 422);
            }

            // Check if combination already exists
            $existingCombination = Kelas::where('tingkat_kelas_id', $tingkatKelas->id)
                ->where('jurusan_id', $jurusan->id)
                ->exists();

            if ($existingCombination) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kombinasi tingkat kelas dan jurusan sudah ada'
                ], 422);
            }

            $namaKelas = $tingkatKelas->tingkat . ' ' . $jurusan->kode_jurusan;

            $kelas = Kelas::create([
                'tingkat_kelas_id' => $tingkatKelas->id,
                'jurusan_id' => $jurusan->id,
                'nama_kelas' => $namaKelas,
                'wali_kelas' => trim($request->wali_kelas),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Kelas {$namaKelas} berhasil ditambahkan",
                'data' => $kelas->load(['tingkatKelas', 'jurusan'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan kelas'
            ], 500);
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $kelas = Kelas::with(['tingkatKelas', 'jurusan'])->findOrFail($id);
            $tingkatKelas = TingkatKelas::orderBy('tingkat')->get();
            $jurusans = Jurusan::orderBy('kode_jurusan')->get();

            return view('dashboard.kelas.edit', compact('kelas', 'tingkatKelas', 'jurusans'));
        } catch (\Exception $e) {
            return redirect()->route('kelas.index')
                ->with('error', 'Data kelas tidak ditemukan.');
        }
    }

    /**
     * Update kelas (handles both AJAX and form submissions)
     */
    public function update(Request $request, $id)
    {
        // For AJAX requests, use different validation
        if ($request->ajax()) {
            $request->validate([
                'tingkat_kelas_id' => 'required|string',
                'jurusan_id' => 'required|string',
                'wali_kelas' => 'required|string|max:255',
            ], [
                'tingkat_kelas_id.required' => 'Tingkat kelas harus dipilih',
                'jurusan_id.required' => 'Jurusan harus dipilih',
                'wali_kelas.required' => 'Nama wali kelas harus diisi',
                'wali_kelas.max' => 'Nama wali kelas maksimal 255 karakter',
            ]);

            try {
                DB::beginTransaction();

                $kelas = Kelas::findOrFail($id);

                // Find tingkat kelas by string
                $tingkatKelas = TingkatKelas::where('tingkat', $request->tingkat_kelas_id)->first();
                if (!$tingkatKelas) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tingkat kelas tidak ditemukan'
                    ], 422);
                }

                // Find jurusan by kode
                $jurusan = Jurusan::where('kode_jurusan', $request->jurusan_id)->first();
                if (!$jurusan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jurusan tidak ditemukan'
                    ], 422);
                }

                // Check if combination already exists (exclude current record)
                $existingCombination = Kelas::where('tingkat_kelas_id', $tingkatKelas->id)
                    ->where('jurusan_id', $jurusan->id)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($existingCombination) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kombinasi tingkat kelas dan jurusan sudah ada'
                    ], 422);
                }

                $namaKelas = $tingkatKelas->tingkat . ' ' . $jurusan->kode_jurusan;

                $kelas->update([
                    'tingkat_kelas_id' => $tingkatKelas->id,
                    'jurusan_id' => $jurusan->id,
                    'nama_kelas' => $namaKelas,
                    'wali_kelas' => trim($request->wali_kelas),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Kelas {$namaKelas} berhasil diperbarui",
                    'data' => $kelas->load(['tingkatKelas', 'jurusan'])
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui kelas'
                ], 500);
            }
        }

        // For regular form submissions
        $request->validate([
            'tingkat_kelas_id' => 'required|exists:tingkat_kelas,id',
            'jurusan_id' => 'required|exists:jurusans,id',
            'wali_kelas' => 'required|string|max:255',
        ], [
            'tingkat_kelas_id.required' => 'Tingkat kelas harus dipilih',
            'tingkat_kelas_id.exists' => 'Tingkat kelas tidak valid',
            'jurusan_id.required' => 'Jurusan harus dipilih',
            'jurusan_id.exists' => 'Jurusan tidak valid',
            'wali_kelas.required' => 'Nama wali kelas harus diisi',
            'wali_kelas.max' => 'Nama wali kelas maksimal 255 karakter',
        ]);

        try {
            DB::beginTransaction();

            $kelas = Kelas::findOrFail($id);

            // Check if combination already exists (exclude current record)
            $existingCombination = Kelas::where('tingkat_kelas_id', $request->tingkat_kelas_id)
                ->where('jurusan_id', $request->jurusan_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($existingCombination) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Kombinasi tingkat kelas dan jurusan sudah ada.');
            }

            // Generate new nama_kelas
            $tingkatKelas = TingkatKelas::find($request->tingkat_kelas_id);
            $jurusan = Jurusan::find($request->jurusan_id);
            $namaKelas = $tingkatKelas->tingkat . ' ' . $jurusan->kode_jurusan;

            $kelas->update([
                'tingkat_kelas_id' => $request->tingkat_kelas_id,
                'jurusan_id' => $request->jurusan_id,
                'nama_kelas' => $namaKelas,
                'wali_kelas' => trim($request->wali_kelas),
            ]);

            DB::commit();

            return redirect()->route('kelas.index')
                ->with('success', "Kelas {$namaKelas} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui kelas.');
        }
    }


    /**
     * Delete kelas via AJAX
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $kelas = Kelas::findOrFail($id);

            // Check if kelas has students
            $jumlahSiswa = $kelas->siswas()->count();
            if ($jumlahSiswa > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Kelas {$kelas->nama_kelas} tidak dapat dihapus karena masih memiliki {$jumlahSiswa} siswa."
                ], 422);
            }

            $namaKelas = $kelas->nama_kelas;
            $kelas->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Kelas {$namaKelas} berhasil dihapus."
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Data kelas tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kelas.'
            ], 500);
        }
    }

    /**
     * Get available jurusan for selected tingkat (for edit form)
     */
    public function getAvailableJurusan(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $tingkatId = $request->tingkat_id;
        $currentKelasId = $request->current_kelas_id;

        if (!$tingkatId) {
            return response()->json([
                'success' => false,
                'message' => 'Tingkat kelas harus dipilih'
            ], 400);
        }

        try {
            // Get all jurusans
            $allJurusans = Jurusan::orderBy('kode_jurusan')->get();

            // Get used combinations for this tingkat (exclude current kelas if editing)
            $usedJurusanIds = Kelas::where('tingkat_kelas_id', $tingkatId)
                ->when($currentKelasId, function ($query, $currentKelasId) {
                    return $query->where('id', '!=', $currentKelasId);
                })
                ->pluck('jurusan_id')
                ->toArray();

            // Filter available jurusans
            $availableJurusans = $allJurusans->reject(function ($jurusan) use ($usedJurusanIds) {
                return in_array($jurusan->id, $usedJurusanIds);
            });

            return response()->json([
                'success' => true,
                'data' => $availableJurusans->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data jurusan'
            ], 500);
        }
    }

    /**
     * Update jurusan via AJAX
     */
    public function updateJurusan(Request $request, $id)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusans,kode_jurusan,' . $id,
            'nama_jurusan' => 'required|string|max:100'
        ], [
            'kode_jurusan.required' => 'Kode jurusan harus diisi',
            'kode_jurusan.unique' => 'Kode jurusan sudah ada',
            'kode_jurusan.max' => 'Kode jurusan maksimal 20 karakter',
            'nama_jurusan.required' => 'Nama jurusan harus diisi',
            'nama_jurusan.max' => 'Nama jurusan maksimal 100 karakter'
        ]);

        try {
            DB::beginTransaction();

            $jurusan = Jurusan::findOrFail($id);
            $jurusan->update([
                'kode_jurusan' => strtoupper(trim($request->kode_jurusan)),
                'nama_jurusan' => trim($request->nama_jurusan)
            ]);
            $kelas = Kelas::where('jurusan_id', $jurusan->id)->get();
            $kelas->each(function ($k) use ($jurusan) {
                $tingkat = $k->tingkatKelas->tingkat;
                $k->nama_kelas = $tingkat . ' ' . $jurusan->kode_jurusan;
                $k->save();
            });

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jurusan berhasil diperbarui',
                'data' => $jurusan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating jurusan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui jurusan'
            ], 500);
        }
    }

    /**
     * Delete tingkat kelas via AJAX
     */
    public function destroyTingkatKelas($id)
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        try {
            DB::beginTransaction();

            $tingkatKelas = TingkatKelas::findOrFail($id);

            // Check if tingkat kelas is being used by any kelas
            $jumlahKelas = $tingkatKelas->kelas()->count();
            if ($jumlahKelas > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tingkat Kelas {$tingkatKelas->tingkat} tidak dapat dihapus karena masih digunakan oleh {$jumlahKelas} kelas."
                ], 422);
            }

            $tingkat = $tingkatKelas->tingkat;
            $tingkatKelas->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Tingkat Kelas {$tingkat} berhasil dihapus."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus tingkat kelas.'
            ], 500);
        }
    }

    public function destroyJurusan($id)
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        try {
            DB::beginTransaction();

            $jurusan = Jurusan::findOrFail($id);

            // Check if jurusan is being used by any kelas
            $jumlahKelas = $jurusan->kelas()->count();
            if ($jumlahKelas > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Jurusan {$jurusan->kode_jurusan} tidak dapat dihapus karena masih digunakan oleh {$jumlahKelas} kelas."
                ], 422);
            }

            $kodeJurusan = $jurusan->kode_jurusan;
            $jurusan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Jurusan {$kodeJurusan} berhasil dihapus."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus jurusan.'
            ], 500);
        }
    }
}
