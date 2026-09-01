<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\RegisterController;
use App\Http\Controllers\Frontend\LoginController;

/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/{slug}', [ProductController::class, 'show'])
    ->name('products.show');

Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->name('cart.add');

Route::post('/cart/buy-now/{product}', [CartController::class, 'buyNow'])
    ->name('cart.buyNow');

Route::post('/cart/remove/{product}', [CartController::class, 'remove'])
    ->name('cart.remove');

Route::get('/category/{slug}', [CategoryController::class, 'show'])
    ->name('categories.show');

Route::get('/page/{slug}', [PageController::class, 'show'])
    ->name('pages.show');

Route::get('/media/{path}', function (string $path) {
    $relativePath = ltrim($path, '/');
    if (str_contains($relativePath, '..')) {
        abort(404);
    }

    $absolutePath = storage_path('app/public/' . $relativePath);
    $resolvedPath = realpath($absolutePath);
    $storageRoot = realpath(storage_path('app/public'));

    if (! $resolvedPath || ! $storageRoot || ! str_starts_with($resolvedPath, $storageRoot) || ! is_file($resolvedPath)) {
        abort(404);
    }

    return response()->file($resolvedPath);
})->where('path', '.*')->name('media.file');

/*
|--------------------------------------------------------------------------
| Customer Guest Routes
|--------------------------------------------------------------------------
|
| These pages can only be opened when the customer is not logged in.
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'register'])
        ->name('register.post');

    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Logged-in Customer Routes
|--------------------------------------------------------------------------
|
| These pages can only be opened after customer login.
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/my-account', function () {
        return view('frontend.account', [
            'user' => auth()->user(),
        ]);
    })->name('account');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(base_path('routes/admin.php'));



