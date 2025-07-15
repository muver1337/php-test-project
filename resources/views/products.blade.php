<h1>Товары и остатки по складам</h1>

@foreach ($products as $product)
    <h3>{{ $product->name }}</h3>
    <ul>
        @forelse ($product->warehouses as $warehouse)
            <li>
                Склад: {{ $warehouse->name }} | Остаток: {{ $warehouse->pivot->stock }}
            </li>
        @empty
            <li>Нет на складе</li>
        @endforelse
    </ul>
@endforeach
