<?php

namespace App\Filament\TenantManager\Resources;

use App\Filament\TenantManager\Resources\SettingResource\Pages;
use App\Filament\TenantManager\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Fieldset::make('Details')
                ->schema([
                    Forms\Components\TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->unique(ignoreRecord:true),
                Forms\Components\Select::make('type')
                    ->label('Field Type')
                    ->searchable()
                    ->options([
                        'text'      => 'Text Input',
                        'boolean'   => 'Boolean',
                        'select'    => 'Select',
                        'file'      => 'File'
                    ])
                    ->reactive(),
                Forms\Components\TextInput::make('group')
                    ->label('Group')
                    ->datalist(Setting::pluck('group')->toArray()),
                Forms\Components\Repeater::make('attributes.options')
                    ->label('default.Options')
                    ->grid(2)
                    ->simple(
                        Forms\Components\TextInput::make('key')
                            ->required(),
                    )
                    ->visible(function(Callable $get){
                        return $get('type')== 'select';
                    })

                ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group')
                    ->label('Group')
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
