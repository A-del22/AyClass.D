<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class GuruController extends Controller
{
    /**
     * Display index page with DataTable
     */
    public function index()
    {
        return view('dashboard.guru.index');
    }

    /**
     * Get data for DataTables AJAX
     */
    public function getGuruData(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $query = User::guru()
            ->select(['id', 'name', 'email', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                return $row->name;
            })
            ->addColumn('email', function ($row) {
                return $row->email;
            })
            ->addColumn('tanggal_dibuat', function ($row) {
                return Carbon::parse($row->created_at)->format('d/m/Y H:i');
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group" role="group">
                        <a href="' . route('guru.edit', $row->id) . '" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteGuru(\'' . $row->id . '\')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new guru
     */
    public function create()
    {
        return view('dashboard.guru.create');
    }

    /**
     * Store a newly created guru in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        try {
            DB::beginTransaction();

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'guru',
            ]);

            DB::commit();

            return redirect()->route('guru.index')
                ->with('success', 'Data guru berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified guru
     */
    public function edit($id)
    {
        $guru = User::guru()->findOrFail($id);
        return view('dashboard.guru.edit', compact('guru'));
    }

    /**
     * Update the specified guru in storage
     */
    public function update(Request $request, $id)
    {
        $guru = User::guru()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($guru->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $guru->update($updateData);

            DB::commit();

            return redirect()->route('guru.index')
                ->with('success', 'Data guru berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified guru from storage
     */
    public function destroy($id)
    {
        try {
            $guru = User::guru()->findOrFail($id);

            DB::beginTransaction();

            $guru->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data guru berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $totalGuru = User::guru()->count();
            $guruBulanIni = User::guru()
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            return response()->json([
                'total_guru' => $totalGuru,
                'guru_bulan_ini' => $guruBulanIni,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('dashboard.guru.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = auth()->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        // Check if new password is same as current password
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password lama']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('guru.change-password')->with('success', 'Password berhasil diubah!');
    }
}