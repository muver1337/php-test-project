<h1>Список складов</h1>

@if($warehouses->isEmpty())
    <p>Склады не найдены.</p>
@else
    <ul>
        @foreach($warehouses as $warehouse)
            <li>
                    {{ $warehouse->name }}
            </li>
        @endforeach
    </ul>
@endif
