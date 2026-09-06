<?php

namespace App\Filament\Resources\Examinations;

use App\Filament\Resources\Examinations\Pages\CreateExamination;
use App\Filament\Resources\Examinations\Pages\EditExamination;
use App\Filament\Resources\Examinations\Pages\ListExaminations;
use App\Filament\Resources\Examinations\Schemas\ExaminationForm;
use App\Filament\Resources\Examinations\Tables\ExaminationsTable;
use App\Models\Examination;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExaminationResource extends Resource
{
    protected static ?string $model = Examination::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return ExaminationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExaminationsTable::configure($table);
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
            'index' => ListExaminations::route('/'),
            'create' => CreateExamination::route('/create'),
            'edit' => EditExamination::route('/{record}/edit'),
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
