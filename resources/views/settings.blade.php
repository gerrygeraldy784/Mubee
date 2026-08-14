@extends('layouts.app')

@section('title', 'Pengaturan - Mubee')

@section('styles')
    .settings-container {
        max-width: 900px;
        margin: 0 auto 50px auto;
        padding: 0 clamp(16px, 4vw, 48px);
    }

    .settings-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
    }

    @media (min-width: 768px) {
        .settings-grid {
            grid-template-columns: 2fr 3fr;
        }
    }

    .settings-card {
        background: var(--card-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 30px;
        backdrop-filter: blur(15px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .settings-section-title {
        font-family: var(--heading-family);
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 24px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        padding-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .settings-section-title i {
        color: var(--primary);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text-muted);
    }

    .form-control {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        padding: 12px 16px;
        color: white;
        font-size: 15px;
        outline: none;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 10px rgba(255, 42, 84, 0.2);
    }

    /* Color Swatch Selectors */
    .color-picker {
        display: flex;
        gap: 15px;
        margin-top: 10px;
    }

    .color-option {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        position: relative;
        border: 3px solid transparent;
        transition: all 0.2s ease;
    }

    .color-option:hover {
        transform: scale(1.1);
    }

    .color-option.active {
        border-color: white;
        box-shadow: 0 0 15px rgba(255,255,255,0.5);
    }

    .color-option[data-color="pink"] { background: linear-gradient(135deg, #ff2a54 0%, #ff6b3d 100%); }
    .color-option[data-color="blue"] { background: linear-gradient(135deg, #2979ff 0%, #00e5ff 100%); }
    .color-option[data-color="purple"] { background: linear-gradient(135deg, #a020f0 0%, #ff007f 100%); }
    .color-option[data-color="green"] { background: linear-gradient(135deg, #00e676 0%, #b2ff59 100%); }

    /* Switch Toggles */
    .switch-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--glass-border);
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .switch-info h4 {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .switch-info p {
        font-size: 12px;
        color: var(--text-muted);
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #333;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background: var(--primary-gradient);
    }

    input:checked + .slider:before {
        transform: translateX(24px);
    }

    .btn-submit {
        width: 100%;
        padding: 14px;
        border-radius: 8px;
        background: var(--primary-gradient);
        color: white;
        border: none;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 42, 84, 0.4);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 42, 84, 0.6);
    }
@endsection

@section('content')
    <div class="settings-container">
        <div class="page-header" style="padding-left: 0;">
            <h1 class="page-title">Pengaturan Akun & Preferensi</h1>
        </div>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="settings-grid">
                <!-- Left Column: Preferences -->
                <div class="settings-card">
                    <h3 class="settings-section-title">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Preferensi Tampilan
                    </h3>

                    <!-- Color Accent Picker -->
                    <div class="form-group">
                        <span class="form-label">Warna Aksen Tema</span>
                        <div class="color-picker">
                            <div class="color-option {{ $setting->theme_accent === 'pink' ? 'active' : '' }}" data-color="pink" onclick="selectColor('pink')"></div>
                            <div class="color-option {{ $setting->theme_accent === 'blue' ? 'active' : '' }}" data-color="blue" onclick="selectColor('blue')"></div>
                            <div class="color-option {{ $setting->theme_accent === 'purple' ? 'active' : '' }}" data-color="purple" onclick="selectColor('purple')"></div>
                            <div class="color-option {{ $setting->theme_accent === 'green' ? 'active' : '' }}" data-color="green" onclick="selectColor('green')"></div>
                        </div>
                        <input type="hidden" name="theme_accent" id="theme_accent_input" value="{{ $setting->theme_accent }}">
                    </div>

                    <!-- Quality Selector -->
                    <div class="form-group" style="margin-top: 30px;">
                        <label class="form-label" for="playback_quality">Kualitas Video Utama</label>
                        <select name="playback_quality" id="playback_quality" class="form-control">
                            <option value="1080p" {{ $setting->playback_quality === '1080p' ? 'selected' : '' }}>Ultra HD (1080p)</option>
                            <option value="720p" {{ $setting->playback_quality === '720p' ? 'selected' : '' }}>High Definition (720p)</option>
                            <option value="480p" {{ $setting->playback_quality === '480p' ? 'selected' : '' }}>Standard Definition (480p)</option>
                        </select>
                    </div>

                    <!-- Language Selector -->
                    <div class="form-group">
                        <label class="form-label" for="language">Bahasa Antarmuka</label>
                        <select name="language" id="language" class="form-control">
                            <option value="id" {{ $setting->language === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                            <option value="en" {{ $setting->language === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                    </div>

                    <!-- Autoplay Switch -->
                    <div class="switch-group">
                        <div class="switch-info">
                            <h4>Autoplay Episode</h4>
                            <p>Putar episode berikutnya otomatis</p>
                        </div>
                        <label class="switch">
                            <input type="hidden" name="autoplay" value="0">
                            <input type="checkbox" name="autoplay" value="1" {{ $setting->autoplay ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Right Column: Profile details -->
                <div class="settings-card">
                    <h3 class="settings-section-title">
                        <i class="fa-solid fa-user-gear"></i> Informasi Profil
                    </h3>

                    @if ($errors->any())
                        <div style="background: rgba(255, 42, 84, 0.15); border: 1px solid var(--primary); padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
                            <ul style="list-style: none; padding: 0;">
                                @foreach ($errors->all() as $error)
                                    <li style="color: #ff4d6d;"><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
                        <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 15px; color: var(--text-muted);">Ubah Kata Sandi (Opsional)</h4>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Kata Sandi Baru</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                    </div>

                    <button type="submit" class="btn-submit" style="margin-top: 20px;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        function selectColor(colorName) {
            // Remove active class from all swatches
            document.querySelectorAll('.color-option').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active class to clicked swatch
            const activeSwatch = document.querySelector(`.color-option[data-color="${colorName}"]`);
            if (activeSwatch) {
                activeSwatch.classList.add('active');
            }
            
            // Set input value
            document.getElementById('theme_accent_input').value = colorName;
        }
    </script>
@endsection
