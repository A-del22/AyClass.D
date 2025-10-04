<div class="leftside-menu">
    @if (auth()->user()->role === 'siswa')
        <a href="{{ route('student.dashboard') }}" class="logo logo-light">
            <span class="logo-lg">
                <img src="{{ asset('logo.png') }}" alt="Company Logo">
            </span>
            <span class="logo-sm">
                <img src="{{ asset('logo.png') }}" alt="Company Logo">
            </span>
        </a>

        <a href="{{ route('student.dashboard') }}" class="logo logo-dark">
            <span class="logo-lg">
                <img src="{{ asset('logo.png') }}" alt="Company Logo">
            </span>
            <span class="logo-sm">
                <img src="{{ asset('logo.png') }}" alt="Company Logo">
            </span>
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="logo logo-light">
            <span class="logo-lg">
                <img src="{{ asset('logo.png') }}" alt="Company Logo">
            </span>
            <span class="logo-sm">
                <img src="{{ asset('logo.png') }}" alt="Company Logo">
            </span>
        </a>

        <a href="{{ route('dashboard') }}" class="logo logo-dark">
            <span class="logo-lg">
                <img src="{{ asset('logo.png') }}" alt="Company Logo">
            </span>
            <span class="logo-sm">
                <img src="{{ asset('logo.png') }}" alt="Company Logo">
            </span>
        </a>
    @endif

    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <ul class="side-nav">
            @if (auth()->user()->role === 'siswa')
                <li class="side-nav-title">Menu Utama</li>
                <li class="side-nav-item">
                    <a href="{{ route('student.dashboard') }}" class="side-nav-link">
                        <i class="ri-dashboard-3-line"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="{{ route('student.profile') }}" class="side-nav-link">
                        <i class="ri-user-line"></i>
                        <span> Profil Saya </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="{{ route('student.attendance.history') }}" class="side-nav-link">
                        <i class="ri-clipboard-line"></i>
                        <span> Presensi Saya </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="{{ route('student.qrcode') }}" class="side-nav-link">
                        <i class="ri-qr-code-line"></i>
                        <span> QR Code Saya </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="{{ route('student.change-password') }}" class="side-nav-link">
                        <i class="ri-lock-password-line"></i>
                        <span> Ubah Password </span>
                    </a>
                </li>
            @else
                <!-- Admin/Guru Menu -->
                <li class="side-nav-title">Main</li>
                <li class="side-nav-item">
                    <a href="{{ route('dashboard') }}" class="side-nav-link">
                        <i class="ri-dashboard-3-line"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarAttendance" aria-expanded="false"
                        aria-controls="sidebarAttendance" class="side-nav-link">
                        <i class="ri-user-location-line"></i>
                        <span> Presensi Siswa </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarAttendance">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{ route('attendance.index') }}">Presensi</a>
                            </li>
                            <li>
                                <a href="{{ route('attendance.reports.index') }}">Rekap & Laporan</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a href="{{ route('siswa.index') }}" class="side-nav-link">
                        <i class="ri-graduation-cap-line"></i>
                        <span> Data Siswa </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="{{ route('guru.change-password') }}" class="side-nav-link">
                        <i class="ri-lock-password-line"></i>
                        <span> Ubah Password </span>
                    </a>
                </li>
                @if (auth()->user()->role === 'admin')
                    <li class="side-nav-title">Admin</li>
                    <li class="side-nav-item">
                        <a href="{{ route('guru.index') }}" class="side-nav-link">
                            <i class="ri-user-star-line"></i>
                            <span> Data Guru </span>
                        </a>
                    </li>
                    <li class="side-nav-item">
                        <a href="{{ route('kelas.index') }}" class="side-nav-link">
                            <i class="ri-building-fill"></i>
                            <span> Data Kelas </span>
                        </a>
                    </li>
                    <li class="side-nav-item">
                        <a href="{{ route('settings.index') }}" class="side-nav-link">
                            <i class="ri-settings-3-line"></i>
                            <span> Pengaturan Aplikasi </span>
                        </a>
                    </li>
                @endif
            @endif
        </ul>
        <div class="clearfix"></div>
    </div>
</div>
