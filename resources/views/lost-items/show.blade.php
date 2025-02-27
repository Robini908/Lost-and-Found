<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Item Details - {{ $item->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            font-size: 18px;
            font-weight: bold;
            color: #4a5568;
            margin-bottom: 10px;
        }
        .section p {
            font-size: 14px;
            color: #4a5568;
        }
        .images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .images img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Lost Item Details</h1>
        </div>

        <!-- Item Title -->
        <div class="section">
            <h2>Item Title</h2>
            <p>{{ $item->title }}</p>
        </div>

        <!-- Description -->
        <div class="section">
            <h2>Description</h2>
            <p>{{ $item->description }}</p>
        </div>

        <!-- Images -->
        @if ($item->images->isNotEmpty())
            <div class="section">
                <h2>Images</h2>
                <div class="images">
                    @foreach ($item->images as $image)
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $item->title }}" style="width: 200px;">
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Location -->
        <div class="section">
            <h2>Location</h2>
            <p>{{ $item->location }}</p>
        </div>

        <!-- Date Information -->
        <div class="section">
            <h2>{{ $item->item_type === 'found' ? 'Date Found' : 'Date Lost' }}</h2>
            <p>
                @if($item->item_type === 'found')
                    {{ $item->date_found ? $item->date_found->format('F j, Y') : 'Not specified' }}
                @else
                    {{ $item->date_lost ? $item->date_lost->format('F j, Y') : 'Not specified' }}
                @endif
            </p>
        </div>

        <!-- Condition -->
        <div class="section">
            <h2>Condition</h2>
            <p>{{ $item->condition }}</p>
        </div>

        <!-- Reported By -->
        <div class="section">
            <h2>Reported By</h2>
            <p>
                @if ($item->is_anonymous)
                    Anonymous
                @else
                    {{ $item->user->name }}
                @endif
            </p>
        </div>
    </div>
</body>
</html>
