<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BlogCsvController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin routes  (prefix: /admin, name: admin.)
|--------------------------------------------------------------------------
*/

// Guest (login)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.attempt');
});

// Authenticated admin area
Route::middleware('admin')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('analytics', [AnalyticsController::class, 'index'])->middleware('permission:dashboard.view')->name('analytics.index');

    // --- Lead / inquiry management ---
    Route::get('inquiries', [InquiryController::class, 'index'])->middleware('permission:inquiries.view')->name('inquiries.index');
    Route::get('inquiries/export', [InquiryController::class, 'export'])->middleware('permission:inquiries.export')->name('inquiries.export');
    Route::get('inquiries/{inquiry}', [InquiryController::class, 'show'])->middleware('permission:inquiries.view')->name('inquiries.show');
    Route::post('inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus'])->middleware('permission:inquiries.manage')->name('inquiries.status');
    Route::post('inquiries/{inquiry}/notes', [InquiryController::class, 'addNote'])->middleware('permission:inquiries.manage')->name('inquiries.notes');
    Route::post('inquiries/{inquiry}/assign', [InquiryController::class, 'assign'])->middleware('permission:inquiries.assign')->name('inquiries.assign');
    Route::get('inquiries/{inquiry}/attachment', [InquiryController::class, 'attachment'])->middleware('permission:inquiries.view')->name('inquiries.attachment');
    Route::delete('inquiries/{inquiry}', [InquiryController::class, 'destroy'])->middleware('permission:inquiries.manage')->name('inquiries.destroy');

    // --- Package manager ---
    Route::get('packages', [PackageController::class, 'index'])->middleware('permission:packages.view')->name('packages.index');
    Route::get('packages/create', [PackageController::class, 'create'])->middleware('permission:packages.create')->name('packages.create');
    Route::post('packages', [PackageController::class, 'store'])->middleware('permission:packages.create')->name('packages.store');
    Route::get('packages/{package}/edit', [PackageController::class, 'edit'])->middleware('permission:packages.edit')->name('packages.edit');
    Route::put('packages/{package}', [PackageController::class, 'update'])->middleware('permission:packages.edit')->name('packages.update');
    Route::post('packages/{package}/toggle', [PackageController::class, 'toggle'])->middleware('permission:packages.edit')->name('packages.toggle');
    Route::delete('packages/{package}', [PackageController::class, 'destroy'])->middleware('permission:packages.delete')->name('packages.destroy');

    // --- Page builder ---
    Route::get('pages', [PageController::class, 'index'])->middleware('permission:pages.view')->name('pages.index');
    Route::get('pages/create', [PageController::class, 'create'])->middleware('permission:pages.create')->name('pages.create');
    Route::post('pages', [PageController::class, 'store'])->middleware('permission:pages.create')->name('pages.store');
    Route::get('pages/{page}/edit', [PageController::class, 'edit'])->middleware('permission:pages.edit')->name('pages.edit');
    Route::put('pages/{page}', [PageController::class, 'update'])->middleware('permission:pages.edit')->name('pages.update');
    Route::delete('pages/{page}', [PageController::class, 'destroy'])->middleware('permission:pages.delete')->name('pages.destroy');
    Route::post('pages/{page}/sections', [PageController::class, 'addSection'])->middleware('permission:pages.edit')->name('pages.sections.add');
    Route::post('pages/{page}/sections/reorder', [PageController::class, 'reorderSections'])->middleware('permission:pages.edit')->name('pages.sections.reorder');
    Route::put('pages/{page}/sections/{section}', [PageController::class, 'updateSection'])->middleware('permission:pages.edit')->name('pages.sections.update');
    Route::post('pages/{page}/sections/{section}/duplicate', [PageController::class, 'duplicateSection'])->middleware('permission:pages.edit')->name('pages.sections.duplicate');
    Route::delete('pages/{page}/sections/{section}', [PageController::class, 'deleteSection'])->middleware('permission:pages.edit')->name('pages.sections.delete');

    // --- Blog manager ---
    Route::get('posts', [PostController::class, 'index'])->middleware('permission:blog.view')->name('posts.index');
    Route::get('posts/create', [PostController::class, 'create'])->middleware('permission:blog.create')->name('posts.create');
    Route::post('posts', [PostController::class, 'store'])->middleware('permission:blog.create')->name('posts.store');
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->middleware('permission:blog.edit')->name('posts.edit');
    Route::put('posts/{post}', [PostController::class, 'update'])->middleware('permission:blog.edit')->name('posts.update');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->middleware('permission:blog.delete')->name('posts.destroy');
    Route::get('posts-export', [PostController::class, 'export'])->middleware('permission:blog.view')->name('posts.export');
    Route::post('posts/bulk', [PostController::class, 'bulk'])->middleware('permission:blog.edit')->name('posts.bulk');
    Route::post('posts/image-upload', [PostController::class, 'imageUpload'])->middleware('permission:blog.edit')->name('posts.image-upload');
    Route::get('posts/{post}/preview', [PostController::class, 'preview'])->middleware('permission:blog.view')->name('posts.preview');
    Route::get('posts/{post}/preview-frame', [PostController::class, 'previewFrame'])->middleware('permission:blog.view')->name('posts.preview-frame');
    Route::get('posts-link-search', [PostController::class, 'linkSearch'])->middleware('permission:blog.view')->name('posts.link-search');
    Route::post('posts/{post}/duplicate', [PostController::class, 'duplicate'])->middleware('permission:blog.create')->name('posts.duplicate');

    // --- Blog categories ---
    Route::get('categories', [CategoryController::class, 'index'])->middleware('permission:blog.view')->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])->middleware('permission:blog.edit')->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->middleware('permission:blog.edit')->name('categories.store');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->middleware('permission:blog.edit')->name('categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->middleware('permission:blog.edit')->name('categories.update');
    Route::post('categories/{category}/toggle', [CategoryController::class, 'toggle'])->middleware('permission:blog.edit')->name('categories.toggle');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:blog.delete')->name('categories.destroy');

    // --- Blog tags ---
    Route::get('tags', [TagController::class, 'index'])->middleware('permission:blog.view')->name('tags.index');
    Route::post('tags', [TagController::class, 'store'])->middleware('permission:blog.edit')->name('tags.store');
    Route::put('tags/{tag}', [TagController::class, 'update'])->middleware('permission:blog.edit')->name('tags.update');
    Route::delete('tags/{tag}', [TagController::class, 'destroy'])->middleware('permission:blog.delete')->name('tags.destroy');

    // --- Blog CSV import / export ---
    Route::get('blog-csv', [BlogCsvController::class, 'index'])->middleware('permission:blog.view')->name('blog-csv.index');
    Route::get('blog-csv/template', [BlogCsvController::class, 'template'])->middleware('permission:blog.view')->name('blog-csv.template');
    Route::get('blog-csv/export', [BlogCsvController::class, 'export'])->middleware('permission:blog.view')->name('blog-csv.export');
    Route::post('blog-csv/preview', [BlogCsvController::class, 'preview'])->middleware('permission:blog.create')->name('blog-csv.preview');
    Route::post('blog-csv/import', [BlogCsvController::class, 'import'])->middleware('permission:blog.create')->name('blog-csv.import');

    // --- Careers / job openings ---
    Route::get('jobs', [JobController::class, 'index'])->middleware('permission:jobs.view')->name('jobs.index');
    Route::get('jobs/create', [JobController::class, 'create'])->middleware('permission:jobs.create')->name('jobs.create');
    Route::post('jobs', [JobController::class, 'store'])->middleware('permission:jobs.create')->name('jobs.store');
    Route::get('jobs/{job}/edit', [JobController::class, 'edit'])->middleware('permission:jobs.edit')->name('jobs.edit');
    Route::put('jobs/{job}', [JobController::class, 'update'])->middleware('permission:jobs.edit')->name('jobs.update');
    Route::post('jobs/{job}/toggle', [JobController::class, 'toggle'])->middleware('permission:jobs.edit')->name('jobs.toggle');
    Route::delete('jobs/{job}', [JobController::class, 'destroy'])->middleware('permission:jobs.delete')->name('jobs.destroy');

    // --- Navigation manager ---
    Route::get('menus', [MenuController::class, 'index'])->middleware('permission:menus.manage')->name('menus.index');
    Route::post('menus', [MenuController::class, 'store'])->middleware('permission:menus.manage')->name('menus.store');
    Route::put('menus/{menu}', [MenuController::class, 'update'])->middleware('permission:menus.manage')->name('menus.update');
    Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->middleware('permission:menus.manage')->name('menus.destroy');

    // --- Settings manager ---
    Route::get('settings', [SettingsController::class, 'edit'])->middleware('permission:settings.manage')->name('settings.edit');
    Route::put('settings', [SettingsController::class, 'update'])->middleware('permission:settings.manage')->name('settings.update');

    // --- SEO manager ---
    Route::get('seo', [SeoController::class, 'index'])->middleware('permission:seo.view')->name('seo.index');
    Route::post('seo/redirects', [SeoController::class, 'storeRedirect'])->middleware('permission:seo.manage')->name('seo.redirects.store');
    Route::delete('seo/redirects/{redirect}', [SeoController::class, 'destroyRedirect'])->middleware('permission:seo.manage')->name('seo.redirects.destroy');

    // --- Media library ---
    Route::get('media', [MediaController::class, 'index'])->middleware('permission:media.view')->name('media.index');
    Route::post('media', [MediaController::class, 'store'])->middleware('permission:media.manage')->name('media.store');
    Route::put('media/{medium}', [MediaController::class, 'update'])->middleware('permission:media.manage')->name('media.update');
    Route::delete('media/{medium}', [MediaController::class, 'destroy'])->middleware('permission:media.manage')->name('media.destroy');

    // --- Activity log ---
    Route::get('activity', [ActivityController::class, 'index'])->middleware('permission:activity.view')->name('activity.index');

    // --- Users ---
    Route::get('users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->middleware('permission:users.manage')->name('users.create');
    Route::post('users', [UserController::class, 'store'])->middleware('permission:users.manage')->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.manage')->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->middleware('permission:users.manage')->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.manage')->name('users.destroy');

    // --- Roles ---
    Route::get('roles', [RoleController::class, 'index'])->middleware('permission:roles.manage')->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->middleware('permission:roles.manage')->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->middleware('permission:roles.manage')->name('roles.store');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:roles.manage')->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.manage')->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.manage')->name('roles.destroy');
});
