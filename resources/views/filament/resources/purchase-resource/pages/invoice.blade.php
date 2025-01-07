<x-filament-panels::page>
    <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow-sm my-6" id="invoice">

        <div class="grid grid-cols-2 items-center">
          <div>
            <!--  Company logo  -->
            <img src="https://upload.wikimedia.org/wikipedia/commons/d/d5/Tailwind_CSS_Logo.svg" alt="company-logo" height="100" width="100">
          </div>

          <div class="text-right">
            <p>
              Tailwind Inc.
            </p>
            <p class="text-gray-500 text-sm">
              sales@tailwindcss.com
            </p>
            <p class="text-gray-500 text-sm mt-1">
              +41-442341232
            </p>
            <p class="text-gray-500 text-sm mt-1">
              VAT: 8657671212
            </p>
          </div>
        </div>

        <!-- Client info -->
        <div class="grid grid-cols-2 items-center mt-8">
          <div>
            <p class="font-bold text-gray-800">
              Bill From :
            </p>
            <p class="text-gray-500">
              {{ $purchase->provider?->name }}
              <br />
              {{ $purchase->provider?->address }}
            </p>
            <p class="text-gray-500">
                {{ $purchase->provider?->email }}
            </p>
          </div>

          <div class="text-right">
            <p class="">
              Invoice number:
              <span class="text-gray-500">{{ $purchase->invoice_no }}</span>
            </p>
            <p>
              Invoice date: <span class="text-gray-500">{{ $purchase->purchase_date }}</span>
              <br />
              Due date:<span class="text-gray-500">31/07/2023</span>
            </p>
          </div>
        </div>

        <!-- Invoice Items -->
        <div class="-mx-10 mt-8 flow-root sm:mx-0">
          <table class="min-w-full">
            <colgroup>
              <col class="w-full sm:w-1/2">
              <col class="sm:w-1/6">
              <col class="sm:w-1/6">
              <col class="sm:w-1/6">
            </colgroup>
            <thead class="border-b border-gray-300 text-gray-900">
              <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Items</th>
                <th scope="col" class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">Quantity</th>
                <th scope="col" class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">Price</th>
                <th scope="col" class="py-3.5 pl-3 pr-4 text-right text-sm font-semibold text-gray-900 sm:pr-0">Amount</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($purchase->products as $product)


              <tr class="border-b border-gray-200">
                <td class="max-w-0 py-5 pl-4 pr-3 text-sm sm:pl-0">
                  <div class="font-medium text-gray-900">{{ $product->product->name }}</div>
                  <div class="mt-1 truncate text-gray-500">No description.</div>
                </td>
                <td class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">{{ $product->quantity }}</td>
                <td class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">RM {{ $product->price}}</td>
                <td class="py-5 pl-3 pr-4 text-right text-sm text-gray-500 sm:pr-0">RM {{$product->quantity * $product->price }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-6 text-right text-sm font-normal text-gray-500 sm:table-cell sm:pl-0">Subtotal</th>
                <th scope="row" class="pl-6 pr-3 pt-6 text-left text-sm font-normal text-gray-500 sm:hidden">Subtotal</th>
                <td class="pl-3 pr-6 pt-6 text-right text-sm text-gray-500 sm:pr-0">RM {{ $purchase->total }}</td>
              </tr>
              <tr>
                <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-4 text-right text-sm font-normal text-gray-500 sm:table-cell sm:pl-0">Tax</th>
                <th scope="row" class="pl-6 pr-3 pt-4 text-left text-sm font-normal text-gray-500 sm:hidden">Tax</th>
                <td class="pl-3 pr-6 pt-4 text-right text-sm text-gray-500 sm:pr-0">-</td>
              </tr>
              <tr>
                <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-4 text-right text-sm font-normal text-gray-500 sm:table-cell sm:pl-0">Discount</th>
                <th scope="row" class="pl-6 pr-3 pt-4 text-left text-sm font-normal text-gray-500 sm:hidden">Discount</th>
                <td class="pl-3 pr-6 pt-4 text-right text-sm text-gray-500 sm:pr-0">- RM {{ $purchase->discount }}</td>
              </tr>
              <tr>
                <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-4 text-right text-sm font-semibold text-gray-900 sm:table-cell sm:pl-0">Total</th>
                <th scope="row" class="pl-6 pr-3 pt-4 text-left text-sm font-semibold text-gray-900 sm:hidden">Total</th>
                <td class="pl-3 pr-4 pt-4 text-right text-sm font-semibold text-gray-900 sm:pr-0">RM {{ $purchase->total - $purchase->discount }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!--  Footer  -->
        <div class="border-t-2 pt-4 text-xs text-gray-500 text-center mt-16">
          Please pay the invoice before the due date. You can pay the invoice by logging in to your account from our client portal.
        </div>

      </div>
</x-filament-panels::page>
