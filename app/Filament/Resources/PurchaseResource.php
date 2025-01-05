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
                            Forms\Components\Repeater::make('product')
                            ->hiddenLabel()
                            ->columns([
                                'md' =>10,
                            ])
                            ->schema([

                                                                Forms\Components\Select::make('product_id')
                                                                    ->label('Name of Product')
                                                                    ->options(Product::pluck('name','id')->toArray())
                                                                    ->searchable()
                                                                    ->required()
                                                                    ->columnSpan([
                                                                        'md' => 5
                                                                    ])
                                                                    ->createOptionForm(function(){
                                                                        $tenantField = [
                                                                            Forms\Components\Hidden::make('tenant_id')
                                                                                ->default(Filament::getTenant()->id)
                                                                                ->label('Tenant')
                                                                        ];
                                                                        return array_merge(ProductResource::getProductForm(),$tenantField);
                                                                    })
                                                                    ->createOptionUsing(function(array $data){
                                                                        $product = Product::create($data);
                                                                        return $product->id;
                                                                    }),
                                                            Forms\Components\TextInput::make('price')
                                                                    ->required()
                                                                    ->numeric()
                                                                    ->prefix('RM ')
                                                                    ->columnSpan([
                                                                        'md' => 2
                                                                    ])
                                                                    ->reactive()
                                                                    ->afterStateUpdated(fn(Callable $get, Set $set) => self::updateFormData($get, $set)),
                                                            Forms\Components\TextInput::make('quantity')
                                                                    ->required()
                                                                    ->numeric()
                                                                    ->columnSpan([
                                                                        'md' => 1
                                                                    ])
                                                                    ->reactive()
                                                                    ->afterStateUpdated(fn(Callable $get, Set $set) => self::updateFormData($get, $set)),
                                                            Forms\Components\TextInput::make('total')
                                                                    ->required()
                                                                    ->numeric()
                                                                    ->prefix('RM ')
                                                                    ->disabled()
                                                                    ->columnSpan([
                                                                        'md' => 2
                                                                    ]),

                                ]),



                    ]),
                Forms\Components\Section::make('Total Details')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->disabled()
                            ->label('Sub Total')
                            ->numeric()
                            ->prefix('RM ')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function(Callable $get,Set $set){
                                $discount = $get('discount') ?? 0;
                                $total_amount = $get('total_amount') ?? 0;

                                $set('nettotal',$total_amount - $discount);
                            }),
                        Forms\Components\TextInput::make('discount')
                            ->label('Discount')
                            ->numeric()
                            ->prefix('RM ')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function(Callable $get,Set $set){
                                $discount = $get('discount') ?? 0;
                                $total_amount = $get('total_amount') ?? 0;

                                $set('nettotal',$total_amount - $discount);
                            }),
                        Forms\Components\TextInput::make('nettotal')
                            ->disabled()
                            ->label('Net Total')
                            ->numeric()
                            ->prefix('RM ')
                            ->required(),

                    ])->columns(3),

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

    public static function updateFormData($get, $set)
    {
        // let's get all the form components..
        $formData = $get("../../");
        $allProducts = $formData['product'] ?? [];
        $grandTotal = 0;
        foreach($allProducts as $product)
        {
            $price = $product['price'] ?? 0;
            $quantity = $product['quantity'] ?? 0;
            $total = $price * $quantity;
            $grandTotal += $total;
        }

        $price = $get('price');
        $quantity = $get('quantity');
        $total = $price * $quantity;

        $set('total',$total);
        $set("../../total_amount",$grandTotal);

        $discount = $get("../../discount");
        $set("../../nettotal",$grandTotal - $discount);
    }
}
