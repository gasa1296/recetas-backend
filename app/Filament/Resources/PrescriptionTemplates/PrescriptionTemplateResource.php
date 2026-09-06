<?php

namespace App\Filament\Resources\PrescriptionTemplates;

use App\Filament\Resources\PrescriptionTemplates\Pages\CreatePrescriptionTemplate;
use App\Filament\Resources\PrescriptionTemplates\Pages\EditPrescriptionTemplate;
use App\Filament\Resources\PrescriptionTemplates\Pages\ListPrescriptionTemplates;
use App\Filament\Resources\PrescriptionTemplates\Schemas\PrescriptionTemplateForm;
use App\Filament\Resources\PrescriptionTemplates\Tables\PrescriptionTemplatesTable;
use App\Models\PrescriptionTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrescriptionTemplateResource extends Resource
{
    protected static ?string $model = PrescriptionTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    public static function form(Schema $schema): Schema
    {
        return PrescriptionTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrescriptionTemplatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MedicamentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrescriptionTemplates::route('/'),
            'create' => CreatePrescriptionTemplate::route('/create'),
            'edit' => EditPrescriptionTemplate::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
