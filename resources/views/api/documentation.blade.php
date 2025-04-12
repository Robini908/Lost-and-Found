<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Lost & Found System</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@4.5.0/swagger-ui.css" />
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            background: #fafafa;
        }

        .swagger-ui .topbar {
            background-color: #2c3e50;
        }

        .swagger-ui .info .title {
            color: #2c3e50;
        }

        .swagger-ui .btn.execute {
            background-color: #3498db;
        }

        .swagger-ui .opblock.opblock-post {
            background: rgba(73, 204, 144, 0.1);
            border-color: #49cc90;
        }

        .swagger-ui .opblock.opblock-get {
            background: rgba(97, 175, 254, 0.1);
            border-color: #61affe;
        }

        .swagger-ui .opblock.opblock-put {
            background: rgba(252, 161, 48, 0.1);
            border-color: #fca130;
        }

        .swagger-ui .opblock.opblock-delete {
            background: rgba(249, 62, 62, 0.1);
            border-color: #f93e3e;
        }

        .swagger-ui .opblock.opblock-patch {
            background: rgba(80, 227, 194, 0.1);
            border-color: #50e3c2;
        }

        .topbar-wrapper img {
            content: url({{ asset('images/logo.png') }});
            height: 40px;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@4.5.0/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@4.5.0/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            // Begin Swagger UI call region
            const ui = SwaggerUIBundle({
                url: "{{ route('api.openapi-spec') }}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                tagsSorter: 'alpha',
                operationsSorter: 'alpha',
                docExpansion: 'none',
                persistAuthorization: true,
                oauth2RedirectUrl: window.location.origin + '/api/oauth2-redirect.html'
            });
            // End Swagger UI call region
            window.ui = ui;
        };
    </script>
</body>
</html>
