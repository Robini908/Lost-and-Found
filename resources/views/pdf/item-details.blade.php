<!-- resources/views/pdf/item-details.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Item Details</title>
</head>
<body>
    <h1>{{ $item->title }}</h1>
    <p>{{ $item->description }}</p>
    <p>Location: {{ $item->location }}</p>
    <p>Date Lost: {{ $item->date_lost }}</p>
    <p>Condition: {{ $item->condition }}</p>
</body>
</html>
