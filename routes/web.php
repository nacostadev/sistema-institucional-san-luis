<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/modulos/{module}', [ModuleController::class, 'index'])
        ->whereIn('module', [
            'usuarios',
            'roles',
            'docentes',
            'estudiantes',
            'cursos',
            'secciones',
            'aulas',
            'horarios',
            'matriculas',
            'evaluaciones',
            'notas',
            'costos',
            'pagos',
        ])
        ->name('modules.show');

    Route::post('/modulos/{module}', [ModuleController::class, 'store'])->name('modules.store');
    Route::put('/modulos/{module}/{id}', [ModuleController::class, 'update'])->name('modules.update');
    Route::delete('/modulos/{module}/{id}', [ModuleController::class, 'destroy'])->name('modules.destroy');
});
