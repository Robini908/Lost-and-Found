<?php

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\LostItemController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\TwilioWebhookController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\OpenAIAssistantController;

Route::post('/chatbot', [ChatbotController::class, 'handle']);
Route::get('/create-assistant', [OpenAIAssistantController::class, 'createAssistant']);
Route::get('/lost-items/{id}', [LostItemController::class, 'show'])->name('lost-items.show');
Route::post('/send-sms', [TwilioController::class, 'sendSMS']);
Route::post('/send-whatsapp', [TwilioController::class, 'sendWhatsAppMessage']);
Route::post('/twilio/webhook', [TwilioWebhookController::class, 'handleIncomingMessage']);
Route::get('/send-test-sms', [TwilioController::class, 'sendTestSMS']);


Route::get('/', function () {
    return view('welcome');
});
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/login/{provider}', [SocialiteController::class, 'redirectToProvider'])->name('social.login');
Route::get('/login/{provider}/callback', [SocialiteController::class, 'handleProviderCallback']);
Route::get('/barcode/print/{barcode}', [BarcodeController::class, 'print'])->name('barcode.print');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/products/report-item', function () {
        return view('products.report-item');
    })->name('products.report-item');

    Route::get('/products/view-items', function () {
        return view('products.view-items');
    })->name('products.view-items');
    Route::get('/products/my-lost-items', function () {
        return view('products.my-lost-items');
    })->name('products.my-reported-items');
});

Route::get('/register', function () {
    $item_id = request('item_id');
    session(['item_id' => $item_id]);
    return view('auth.register');
})->name('register');

// Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/manage-usertypes', function () {
        return view('admin.manage-usertypes');
    })->name('admin.manage-usertypes');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/users/general-settings', function () {
        return view('users.general-settings');
    })->name('users.general-settings');
});
