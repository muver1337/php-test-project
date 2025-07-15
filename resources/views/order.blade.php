<form method="GET" action="{{ route('orders.index') }}">
    <p>Фильтрация по статусу заказа: <select name="status">
            <option value="">-- Все статусы --</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>active</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>completed</option>
            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>canceled</option>
        </select></p>
    <p>Фильтрация по дате создания заказа: <input type="date" name="date_from" value="{{ request('date_from') }}"></p>
    <p>Фильтрация по дате выполения заказа: <input type="date" name="date_to" value="{{ request('date_to') }}"></p>
    <p> Пагинация <select name="per_page">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
        <button type="submit">Применить</button>
</form>

@foreach ($orders as $order)
    <div>
        Заказ #{{ $order->id }} | Статус: {{ $order->status }} <br>
        Дата создания заказа: {{ $order->created_at->format('Y-m-d - H:i')}} <br> Дата закрытия заказа: {{ $order->completed_at?->format('Y-m-d - H:i') ?? '-' }}
    </div>
    <hr>
@endforeach

{{ $orders->links() }}
