<div>
{{$getRecord()->service->upt ?? ''}}
    @if($getRecord()->subTransactions->isNotEmpty())
        @foreach($getRecord()->subTransactions as $subTransaction)
            @php
                $product = 'N\A';
                if (isset($subTransaction->service->product_text)) {
                    $productParts = explode('-', $subTransaction->service->product_text);
                    $product = $productParts[0] ?? '-';
                }
            @endphp
            <p>
                - {{ $product }} <br>
            </p>
        @endforeach
    @else
        @php
            $product = '-';
            if (isset($getRecord()->service->product_text)) {
                $productParts = explode('-', $getRecord()->service->product_text);
                $product = $productParts[0] ?? '-';
            }
        @endphp
        <p>
            - {{ $product }} <br>
        </p>
    @endif
</div>
