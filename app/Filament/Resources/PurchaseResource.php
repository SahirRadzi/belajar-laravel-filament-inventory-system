<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Set;
use App\Models\Category;
use App\Models\Purchase;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PurchaseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PurchaseResource\RelationManagers;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function getNavigationLabel(): string
    {
        return 'Purchase';
    }

    protected static ?string $navigationGroup = 'Transactions';

    public static function form(Form $form): Form
    {
        /**
         * We will have 3 parts.
         * 1. About providers.
         * 2. Multiple products.
         * 3. Total and Sub-total.
         */

        return $form
            ->schema([
                Forms\Components\Section::make('Provider Details')
                    ->schema([
                        Forms\Components\Select::make('provider_id')
                            ->relationship('provider','name')
                            ->createOptionForm(function(){
                                $tenantField = [
                                    Forms\Components\Hidden::make('tenant_id')
                                        ->default(Filament::getTenant()->id)
                                        ->label('Tenant')
                                ];
                                return array_merge(CustomerResource::getCustomerFormSchema(),$tenantField);
                            })
                            ->placeholder('-- Choose your provider --')
                            ->required(),
                        Forms\Components\TextInput::make('invoice_no')
                            ->required(),
                        Forms\Components\DatePicker::make('purchase_date')
                            ->required(),
                    ])->columns(3),
                Forms\Components\Section::make('Product Details')
                    ->description('select the list product')
                    ->schema([
                            Forms\Components\Repeater::make('Product')
                            ->schema([

                                                                Forms\Components\Select::make('product_id')
                                                                    ->options(Product::pluck('name','id')->toArray())
                                                                    ->searchable()
                                                                    ->required()
                                                                    ->createOptionForm(function(){
                                                                        //     Forms\Components\TextInput::make('name')
                                                                        //     ->label('Name')
                                                                        //     ->placeholder('Enter name of product')
                                                                        //     ->required(),
                                                                        // Forms\Components\TextInput::make('code')
                                                                        //     ->label('Code')
                                                                        //     ->placeholder('Enter 8 characters code of product')
                                                                        //     ->maxLength(8)
                                                                        //     ->required(),
                                                                        // Forms\Components\Select::make('category_id')
                                                                        //     ->placeholder('-- Choose your category of product --')
                                                                        //     ->options(function(){
                                                                        //         return Category::pluck('name','id')->toArray();
                                                                        //     })
                                                                        //     ->searchable()
                                                                        //     ->required(),
                                                                        // Forms\Components\Select::make('unit_id')
                                                                        //     ->placeholder('-- Choose your unit of product --')
                                                                        //     ->options(function(){
                                                                        //         return Unit::pluck('name','id')->toArray();
                                                                        //     })
                                                                        //     ->searchable()
                                                                        //     ->required(),
                                                                        // Forms\Components\TextInput::make('price')
                                                                        //     ->label('Price (RM)')
                                                                        //     ->placeholder('Enter price of product')
                                                                        //     ->required()
                                                                        //     ->numeric()
                                                                        //     ->step(0.01),
                                                                        // Forms\Components\TextInput::make('quantity')
                                                                        //     ->label('Quantity')
                                                                        //     ->placeholder('Enter quantity of product')
                                                                        //     ->numeric()
                                                                        //     ->required(),
                                                                        // Forms\Components\TextInput::make('safety_stock')
                                                                        //     ->label('Safety Stock')
                                                                        //     ->placeholder('Enter safety stock of product')
                                                                        //     ->helperText(new HtmlString('<strong>Info:</strong> Minimum stock to be stored.'))
                                                                        //     ->numeric(),

                                                                    }),
                                                            Forms\Components\TextInput::make('price')
                                                                    ->required()
                                                                    ->numeric()
                                                                    ->reactive()
                                                                    ->afterStateUpdated(function(callable $get, Set $set){
                                                                       $price = $get('price');
                                                                       $quantity = $get('quantity');
                                                                       $total = $price * $quantity;

                                                                       $set('total',$total);
                                                                    }),
                                                            Forms\Components\TextInput::make('quantity')
                                                                    ->required()
                                                                    ->numeric()
                                                                    ->reactive()
                                                                    ->afterStateUpdated(function(callable $get, Set $set){
                                                                        $price = $get('price');
                                                                        $quantity = $get('quantity');
                                                                        $total = $price * $quantity;

                                                                        $set('total',$total);
                                                                     }),
                                                            Forms\Components\TextInput::make('total')
                                                                    ->required()
                                                                    ->numeric()
                                                                    ->disabled(),

                                ])->columns(4),



                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
