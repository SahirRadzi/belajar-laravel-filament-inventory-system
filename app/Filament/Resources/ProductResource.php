<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;
use Filament\Facades\Filament;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function getNavigationLabel(): string
    {
        return 'Manage Products';
    }

    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getProductForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->searchable()
                    ->sortable()
                    ->prefix('RM '),
                Tables\Columns\TextColumn::make('safety_stock')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getProductForm()
    {
        return [
            Forms\Components\Section::make()
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Enter name of product')
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->label('Code')
                    ->placeholder('Enter 8 characters code of product')
                    ->maxLength(8)
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->placeholder('-- Choose your category of product --')
                    ->options(Category::pluck('name','id')->toArray())
                    ->createOptionForm(function(){
                        $tenantField = [
                            Forms\Components\Hidden::make('tenant_id')
                                ->default(Filament::getTenant()->id)
                                ->label('Tenant')
                        ];
                        return array_merge(CategoryResource::getCategoryForm(),$tenantField);
                    })
                    ->createOptionUsing(function(array $data){
                      $category = Category::create($data);
                      return $category->id;
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('unit_id')
                    ->placeholder('-- Choose your unit of product --')
                    ->options(Unit::pluck('name','id')->toArray())
                    ->createOptionForm(function(){
                        $tenantField = [
                            Forms\Components\Hidden::make('tenant_id')
                                ->default(Filament::getTenant()->id)
                                ->label('Tenant')
                        ];
                        return array_merge(UnitResource::getUnitForm(),$tenantField);
                    })
                    ->createOptionUsing(function(array $data){
                        $Unit = Unit::create($data);
                        return $Unit->id;
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Price (RM)')
                    ->placeholder('Enter price of product')
                    ->required()
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantity')
                    ->placeholder('Enter quantity of product')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('safety_stock')
                    ->label('Safety Stock')
                    ->placeholder('Enter safety stock of product')
                    ->helperText(new HtmlString('<strong>Info:</strong> Minimum stock to be stored.'))
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Enter description of product'),
                Forms\Components\KeyValue::make('data')
                    ->label('Extra Properties'),


            ])
        ];
    }
}
