<?php

use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\CareerController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\InquiryController;
use App\Http\Controllers\Site\PackageController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Technical SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Packages & pricing
Route::get('/pricing', [PackageController::class, 'index'])->name('pricing');
Route::get('/packages/{slug}', [PackageController::class, 'show'])->name('packages.show');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{tag:slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Contact + inquiry funnel
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/inquiry', [InquiryController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('inquiry.store');
Route::get('/thank-you/{reference}', [InquiryController::class, 'thanks'])->name('inquiry.thanks');

// Careers
Route::get('/careers', [CareerController::class, 'index'])->name('careers.index');
Route::get('/careers/{slug}', [CareerController::class, 'show'])->name('careers.show');

/*
|--------------------------------------------------------------------------
| CMS pages (about, services, server-hosting, privacy, terms, and any
| admin-created page). Registered as a fallback so it never shadows
| /admin or other defined routes, and also serves as the 404 handler.
|--------------------------------------------------------------------------
*/
Route::fallback([PageController::class, 'show']);
