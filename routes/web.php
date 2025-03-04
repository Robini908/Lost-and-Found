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
use App\Http\Controllers\ItemMatchingController;
use App\Http\Controllers\TwilioWebhookController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\OpenAIAssistantController;
use App\Http\Middleware\SecurityHeaders;
use App\Livewire\ManageUserTypes;
use App\Livewire\ManageUsers;
use App\Livewire\Settings;
use App\Livewire\VerifyClaim;
use App\Livewire\Analytics;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;

Route::post('/chatbot', [ChatbotController::class, 'handle']);
Route::get('/create-assistant', [OpenAIAssistantController::class, 'createAssistant']);
Route::get('/lost-items/{hashedId}', [LostItemController::class, 'show'])->name('lost-items.show');
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
Route::get('/products/my-lost-items', function () {
    return view('products.my-lost-items');
})->name('products.my-lost-items');
Route::get('/login/{provider}', [SocialiteController::class, 'redirectToProvider'])->name('social.login');
Route::get('/login/{provider}/callback', [SocialiteController::class, 'handleProviderCallback']);
Route::get('/barcode/print/{barcode}', [BarcodeController::class, 'print'])->name('barcode.print');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/products/report-item/{mode?}', function ($mode = null) {
        return view('products.report-item', ['mode' => $mode]);
    })->name('products.report-item');

    Route::get('/rewards', function () {
        return view('rewards');
    })->name('rewards');

    Route::get('/products/report-found-item', function () {
        return view('products.report-found-item');
    })->name('products.report-found-item');

    Route::get('/products/view-items', function () {
        return view('products.view-items');
    })->name('products.view-items');

    Route::get('/products/my-reported-items', function () {
        return view('products.my-lost-items');
    })->name('products.my-reported-items');

    Route::get('/match-items', function () {
        return view('match-items');
    })->name('match-items');

    Route::get('/report-lost-item', function () {
        return view('products.report-item');
    })->name('report-lost-item');

    Route::get('/lost-items/{id}', [LostItemController::class, 'show'])
        ->name('lost-items.show');
    Route::get('/lost-items/{id}/details', [LostItemController::class, 'details'])
        ->name('lost-items.details');
});

Route::get('/matched-items', function () {
    return view('matched-items');
})->name('matched-items');

Route::get('/register', function () {
    $item_id = request('item_id');
    session(['item_id' => $item_id]);
    return view('auth.register');
})->name('register');

// User routes
Route::middleware(['auth'])->group(function () {
    Route::get('/users/general-settings', function () {
        return view('users.settings');
    })->name('users.general-settings');
});

// Admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', ManageUsers::class)->name('admin.manage-users');
    Route::get('/admin/settings', Settings::class)->name('settings');
});

// Moderator routes
Route::middleware(['auth'])->group(function () {
    Route::get('/claims/verify/{id}', function ($id) {
        return view('claims.verify', ['id' => $id]);
    })->name('claims.verify');

    Route::get('/analytics', function () {
        return view('admin.analytics');
    })->name('analytics');
});

// Super admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/roles', ManageUserTypes::class)->name('roles.index');
});

Route::middleware(['auth', 'role:admin|superadmin'])->group(function () {
    Route::get('/management', function () {
        return view('management', [
            'component' => 'management-settings'
        ]);
    })->name('management');
});

Route::middleware([
    'auth:sanctum',
    'verified',
    'security.headers',
    'xss.protect',
    'sql.protect',
    'rate.limit'
])->group(function () {
    // Route::get('/rewards', function () {
    //     return view('rewards');
    // })->name('rewards');

    // Item Claim Verification Routes
    // Route::get('/claims/verify/{claimId}', function ($claimId) {
    //     return view('claims.verify', ['claimId' => $claimId]);
    // })->name('claims.verify');

    // My Reported Items Routes
    Route::get('/my-reported-items', function () {
        return view('products.my-lost-items');
    })->middleware(['auth', 'verified'])->name('products.my-reported-items');

    Route::get('/edit-item/{itemId}', function ($itemId) {
        return view('products.edit-item', ['itemId' => $itemId]);
    })->middleware(['auth', 'verified'])->name('products.edit-item');
});

Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/success-stories', [PageController::class, 'successStories'])->name('success-stories');
Route::get('/faqs', [PageController::class, 'faqs'])->name('faqs');
Route::get('/report-item', [PageController::class, 'reportItem'])->name('report-item');

Route::middleware('guest')->group(function () {
    Route::post('login', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'store'])
        ->middleware(['recaptcha']);

    Route::post('register', [\Laravel\Fortify\Http\Controllers\RegisteredUserController::class, 'store'])
        ->middleware(['recaptcha']);
});
