<?php

namespace App\Filament\Pendaftar\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email address')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable()
            ->required();
    }

    protected static string $view = 'filament.pendaftar.pages.auth.login';

    public function getHeading(): string
    {
        return 'SMAN Taruna Nala Jawa Timur';
    }

    public function getTitle(): string
    {
        return 'Login - SPMB SMAN Taruna Nala Jawa Timur';
    }

    public function hasPasswordResetLink(): bool
    {
        return true; // ← Ini yang membuat link muncul
    }
}