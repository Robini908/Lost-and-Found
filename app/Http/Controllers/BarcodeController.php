<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class BarcodeController extends Controller
{
    public function print($barcode)
    {
        // Generate barcode SVG
        $barcodeSvg = DNS1D::getBarcodeSVG($barcode, 'C39');

        // Return a view with the barcode
        return view('barcode.print', compact('barcodeSvg'));
    }
}
