<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .barcode-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="barcode-container">
        {!! $barcodeSvg !!}
        <p class="mt-4 text-lg font-semibold">{{ $barcode }}</p>
    </div>

    <script>
        // Automatically print the page
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
