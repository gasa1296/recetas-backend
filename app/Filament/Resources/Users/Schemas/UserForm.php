<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('identification')
                    ->required(),
                Textarea::make('phone')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Section::make('Certificado Digital')
                    ->description('Configuración del certificado digital para firma electrónica')
                    ->schema([
                        FileUpload::make('certificate_path')
                            ->label('Certificado (.crt, .pem)')
                            ->acceptedFileTypes(['application/x-x509-ca-cert', 'application/pem-certificate-chain', 'application/octet-stream'])
                            ->directory('certificates')
                            ->visibility('private')
                            ->columnSpanFull(),
                        FileUpload::make('certificate_key_path')
                            ->label('Llave Privada (.key, .pem)')
                            ->acceptedFileTypes(['application/x-pem-key', 'application/octet-stream'])
                            ->directory('certificates')
                            ->visibility('private')
                            ->columnSpanFull(),
                        DateTimePicker::make('certificate_expires_at')
                            ->label('Fecha de expiración del certificado')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
