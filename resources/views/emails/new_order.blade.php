<!DOCTYPE html>
<html>
<head>
    <title>New Order Confirmation</title>
</head>
<body>
    <h1>New Order</h1>

    <p>Detalii comandă:</p>

    <p>Nume: {{ $order->first_name }} {{ $order->last_name }}</p>
    <p>Email: {{ $order->email }}</p>
    <p>Adresă: {{ $order->address }}</p>
    <p>Țară: {{ $order->country }}</p>
    <p>Oraș: {{ $order->city }}</p>
    <p>Cod Poștal: {{ $order->zip_code }}</p>
    <p>Telefon: {{ $order->phone }}</p>
    
    <p>Total Preț: {{ $order->total_price }}</p>
</body>
</html>
