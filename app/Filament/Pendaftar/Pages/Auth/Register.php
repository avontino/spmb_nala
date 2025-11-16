<?php

namespace App\Filament\Pendaftar\Pages\Auth;

use App\Models\User;
use App\Models\Pendaftaran;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Notifications\Notification;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getNikFormComponent(),
                        $this->getKKFormComponent(),
                        $this->getNisnFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Nama Lengkap')
            ->required()
            ->minLength(3)
            ->maxLength(255)
            ->regex('/^[a-zA-Z\s]+$/')
            ->validationMessages([
                'regex' => 'Nama hanya boleh berisi huruf alfabet.',
                'min' => 'Nama minimal 3 huruf.',
            ])
            ->helperText('Ketik nama lengkap sesuai ijazah (minimal 3 huruf, hanya huruf alfabet)');
    }

protected function getNikFormComponent(): Component
{
    return TextInput::make('nik')
        ->label('NIK (Nomor Induk Kependudukan)')
        ->required()
        ->numeric()
        ->length(16)
        ->maxLength(16)
        ->unique(User::class, 'nik')
        ->validationMessages([
            'length' => 'NIK harus 16 digit.',
            'unique' => 'NIK sudah terdaftar sebelumnya.',
            'numeric' => 'NIK hanya boleh berisi angka.',
        ])
        ->helperText('Masukkan 16 digit NIK sesuai Kartu Keluarga (KK)')
        ->reactive()
        ->afterStateUpdated(function ($state, callable $set) {
            if (strlen($state) > 16) {
                $set('nik', substr($state, 0, 16));
            }
        })
        ->extraInputAttributes([
            'maxlength' => 16,
            'x-bind:style' => "(\$wire.data.nik && \$wire.data.nik.length > 16) ? 'border-color: rgb(239 68 68) !important; color: rgb(220 38 38) !important;' : ''",
        ])
        ->suffixIcon(function ($state) {
            if (strlen($state ?? '') > 16) {
                return 'heroicon-o-x-circle';
            }
            if (strlen($state ?? '') === 16) {
                return 'heroicon-o-check-circle';
            }
            return null;
        })
        ->suffixIconColor(function ($state) {
            if (strlen($state ?? '') > 16) {
                return 'danger';
            }
            if (strlen($state ?? '') === 16) {
                return 'success';
            }
            return null;
        });
}

        protected function getKKFormComponent(): Component
    {
        return TextInput::make('nokk')
            ->label('Nomor KK (Kartu Keluarga)')
            ->required()
            ->numeric()
            ->unique(User::class, 'nokk')
            ->validationMessages([
                'unique' => 'Nomor KK sudah terdaftar sebelumnya.',
                'numeric' => 'Nomor KK hanya boleh berisi angka.',
            ])
            ->helperText('Masukkan Nomor Kartu Keluarga sesuai Kartu Keluarga (KK)');
    }

protected function getNisnFormComponent(): Component
{
    return TextInput::make('nisn')
        ->label('NISN (Nomor Induk Siswa Nasional)')
        ->required()
        ->numeric()
        ->length(10)
        ->maxLength(10)
        ->unique(User::class, 'nisn')
        ->validationMessages([
            'length' => 'NISN harus 10 digit.',
            'unique' => 'NISN sudah terdaftar sebelumnya.',
            'numeric' => 'NISN hanya boleh berisi angka.',
        ])
        ->helperText('Masukkan 10 digit NISN')
        ->reactive()
        ->afterStateUpdated(function ($state, callable $set) {
            if (strlen($state) > 10) {
                $set('nisn', substr($state, 0, 10));
            }
        })
        ->extraInputAttributes([
            'maxlength' => 10,
            'x-bind:style' => '($wire.data.nisn && $wire.data.nisn.length > 10) ? "border-color: rgb(239 68 68) !important; color: rgb(220 38 38) !important;" : ""',
        ])
        ->suffixIcon(function ($state) {
            if (strlen($state ?? '') > 10) {
                return 'heroicon-o-x-circle';
            }
            if (strlen($state ?? '') === 10) {
                return 'heroicon-o-check-circle';
            }
            return null;
        })
        ->suffixIconColor(function ($state) {
            if (strlen($state ?? '') > 10) {
                return 'danger';
            }
            if (strlen($state ?? '') === 10) {
                return 'success';
            }
            return null;
        });
}

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email Aktif (WAJIB Gmail dan Email Milik Siswa Sendiri)')
            ->email()
            ->required()
            ->unique(User::class, 'email')
            ->endsWith('@gmail.com')
            ->validationMessages([
                'unique' => 'Email sudah digunakan pendaftar lain.',
                'ends_with' => 'Harus menggunakan Gmail (@gmail.com).',
            ])
            ->helperText('Gunakan alamat Gmail aktif (contoh: nama@gmail.com)');
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->required()
            ->rule(Password::default()->min(6))
            ->same('passwordConfirmation')
            ->validationAttribute('password')
            ->validationMessages([
                'min' => 'Password minimal 6 karakter.',
            ])
            ->helperText('Buat password minimal 6 karakter');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Konfirmasi Password')
            ->password()
            ->required()
            ->dehydrated(false)
            ->validationMessages([
                'same' => 'Password tidak sama.',
            ])
            ->helperText('Ketik ulang password yang sama');
    }

    protected function handleRegistration(array $data): User
    {
        // Create user
        $user = User::create([
            'name' => $data['name'],
            'nik' => $data['nik'],
            'nokk' => $data['nokk'],
            'nisn' => $data['nisn'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'pendaftar',
        ]);

        // ✅ FIX: Check if pendaftaran already exists before creating
        // Ini mencegah duplikasi jika ada observer/event yang juga membuat pendaftaran
        $existingPendaftaran = Pendaftaran::where('user_id', $user->id)->first();
        
        if (!$existingPendaftaran) {
            // Auto create pendaftaran record
            Pendaftaran::create([
                'user_id' => $user->id,
                'status' => 'draft',
            ]);
            
            \Illuminate\Support\Facades\Log::info('✅ Pendaftaran created for user', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        } else {
            \Illuminate\Support\Facades\Log::warning('⚠️ Pendaftaran already exists, skipping creation', [
                'user_id' => $user->id,
                'pendaftaran_id' => $existingPendaftaran->id
            ]);
        }

        // Notifikasi sukses
        Notification::make()
            ->title('Registrasi Berhasil!')
            ->success()
            ->body('Selamat datang! Silakan lengkapi formulir pendaftaran Anda.')
            ->send();

        return $user;
    }
}