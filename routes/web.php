<?php

use App\Http\Controllers\AccountabilityFileController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetModelController;
use App\Http\Controllers\AssetTransferController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Assets
    Route::get('assets/transmittal', [AssetController::class, 'transmittal'])->name('assets.transmittal');
    Route::get('assets/gate-pass', [AssetController::class, 'gatePass'])->name('assets.gate-pass');
    Route::get('assets/{asset}/label', [AssetController::class, 'label'])->name('assets.label');
    Route::delete('assets', [AssetController::class, 'bulkDestroy'])->name('assets.bulk-destroy');
    Route::resource('assets', AssetController::class);
    Route::post('assets/{asset}/transfer', [AssetTransferController::class, 'store'])->name('assets.transfer');
    Route::post('assets/{asset}/files', [AccountabilityFileController::class, 'store'])->name('assets.files.store');
    Route::get('assets/{asset}/files/{file}/download', [AccountabilityFileController::class, 'download'])->name('assets.files.download');
    Route::delete('assets/{asset}/files/{file}', [AccountabilityFileController::class, 'destroy'])->name('assets.files.destroy');

    // Companies, Categories, Locations & Asset Models
    Route::resource('companies', CompanyController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('locations', LocationController::class)->except(['show']);
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('asset-models', AssetModelController::class)
        ->parameters(['asset-models' => 'assetModel'])
        ->except(['show']);

    // Users (Admin only)
    Route::resource('users', UserController::class)->except(['show']);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/assets/pdf', [ReportController::class, 'assetsPdf'])->name('reports.assets.pdf');
    Route::get('reports/assets/excel', [ReportController::class, 'assetsExcel'])->name('reports.assets.excel');
    Route::get('reports/assets/csv', [ReportController::class, 'assetsCsv'])->name('reports.assets.csv');

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{activity}', [AuditLogController::class, 'show'])->name('audit-logs.show');

    // Trash / Restore
    Route::get('trash', [TrashController::class, 'index'])->name('trash.index');
    Route::post('trash/{type}/{id}/restore', [TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('trash/{type}/{id}', [TrashController::class, 'forceDelete'])->name('trash.force-delete');
});

require __DIR__.'/auth.php';
