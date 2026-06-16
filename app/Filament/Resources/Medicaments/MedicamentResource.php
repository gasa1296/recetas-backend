<?php

namespace App\Filament\Resources\Medicaments;

use App\Filament\Resources\Medicaments\Pages\CreateMedicament;
use App\Filament\Resources\Medicaments\Pages\EditMedicament;
use App\Filament\Resources\Medicaments\Pages\ListMedicaments;
use App\Filament\Resources\Medicaments\Schemas\MedicamentForm;
use App\Filament\Resources\Medicaments\Tables\MedicamentsTable;
use App\Models\Medicament;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MedicamentResource extends Resource
{
    protected static ?string $model = Medicament::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Medicament';

    public static function form(Schema $schema): Schema
    {
        return MedicamentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicamentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedicaments::route('/'),
            'create' => CreateMedicament::route('/create'),
            'edit' => EditMedicament::route('/{record}/edit'),
        ];
    }
}
