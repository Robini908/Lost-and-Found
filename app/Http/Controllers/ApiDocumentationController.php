<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ApiDocumentationController extends Controller
{
    /**
     * Display the Swagger UI for API documentation
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('api.documentation');
    }

    /**
     * Return the OpenAPI JSON specification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function openApiSpec()
    {
        $specPath = public_path('swagger/openapi.json');

        if (!file_exists($specPath)) {
            return response()->json(['error' => 'OpenAPI specification not found'], 404);
        }

        $spec = json_decode(file_get_contents($specPath), true);
        return response()->json($spec);
    }
}
