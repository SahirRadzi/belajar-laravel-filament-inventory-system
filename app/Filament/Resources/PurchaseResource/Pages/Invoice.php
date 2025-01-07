<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Models\Purchase;
use Filament\Resources\Pages\Page;

class Invoice extends Page
{
    protected static string $resource = PurchaseResource::class;

    public $record;
    public $purchase;

    public function mount($record)
    {
        $this->record = $record;
        $this->purchase = Purchase::with(['provider','products'])->find($record);
    }

    protected static string $view = 'filament.resources.purchase-resource.pages.invoice';
}