<?php

use App\Http\Controllers\Api\SitemapController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/', PublicPageController::class);
Route::get('/{path}', PublicPageController::class)
    ->where('path', '(?!(?:api|storage|assets)(?:/|$)).+');
