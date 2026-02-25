<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Landing Page (Redirect to Login)
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/test', function () {
    return view('test');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/reset', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// Public Waitlist Routes
Route::get('/waitlist', [\App\Http\Controllers\WaitlistController::class, 'create'])->name('waitlist.create');
Route::post('/waitlist', [\App\Http\Controllers\WaitlistController::class, 'store'])->name('waitlist.store');

// Public Support Ticket Route (Anonymous with Throttle)
Route::post('/report-issue', [App\Http\Controllers\TicketController::class, 'store'])
    ->middleware('throttle:3,1')
    ->name('tickets.store');

// Public API for Reports/Logs (Accessible by guests)
Route::post('/api/tickets', [App\Http\Controllers\DeveloperController::class, 'storeTicket'])->name('api.tickets.store');
Route::post('/api/log-error', [App\Http\Controllers\DeveloperController::class, 'logError'])->name('api.logs.store');


// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/remember-session', [AuthController::class, 'rememberSession'])->name('auth.remember');
    
    // Notificaciones
    Route::get('/notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    
    Route::get('/payments/{id}/proof', [PaymentController::class, 'showProof'])->name('payments.showProof');
    
    // Documents Download (Global Auth)
    Route::get('/documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');

    // Patient Routes
    Route::middleware(['role:paciente'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'patientDashboard'])->name('patient.dashboard');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/payments/create/{appointment_id}', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('/account', [AuthController::class, 'destroyAccount'])->name('patient.account.destroy');
        Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::post('/appointments/cancel-projected', [AppointmentController::class, 'cancelProjected'])->name('appointments.cancelProjected');
        Route::post('/appointments/cancel-fixed', [AppointmentController::class, 'cancelFixedReservation'])->name('appointments.cancelFixed');
        Route::post('/appointments/upload-proof', [AppointmentController::class, 'uploadProof'])->name('appointments.uploadProof');
        Route::get('/documents', [DashboardController::class, 'patientDocuments'])->name('patient.documents');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [DashboardController::class, 'adminHome'])->name('admin.home');
        Route::get('/admin/dashboard', [DashboardController::class, 'adminHome'])->name('admin.dashboard');
        Route::get('/admin/agenda', [DashboardController::class, 'adminAgenda'])->name('admin.agenda');
        Route::get('/admin/agenda/day-details', [DashboardController::class, 'getAgendaDayDetails'])->name('admin.agenda.day-details');
        Route::get('/admin/pacientes', [DashboardController::class, 'adminPacientes'])->name('admin.pacientes');
        Route::get('/admin/pagos', [DashboardController::class, 'adminPagos'])->name('admin.pagos');
        Route::get('/admin/turnos', [DashboardController::class, 'adminTurnos'])->name('admin.turnos');
        Route::get('/admin/documentos', [DashboardController::class, 'adminDocumentos'])->name('admin.documentos');
        Route::get('/admin/waitlist', [DashboardController::class, 'adminWaitlist'])->name('admin.waitlist');
        Route::get('/admin/configuracion', [DashboardController::class, 'adminConfiguracion'])->name('admin.configuracion');
        Route::get('/admin/historial', [DashboardController::class, 'adminHistorial'])->name('admin.historial');
        // Clinical History Routes
        Route::get('/admin/pacientes/{pacienteId}/historia-clinica', [App\Http\Controllers\ClinicalHistoryController::class, 'index'])->name('admin.clinical-history.index');
        Route::get('/admin/pacientes/{userId}/historia-clinica/initialize', [App\Http\Controllers\ClinicalHistoryController::class, 'initialize'])->name('admin.clinical-history.initialize');
        Route::post('/admin/pacientes/{pacienteId}/historia-clinica/{turnoId}', [App\Http\Controllers\ClinicalHistoryController::class, 'store'])->name('admin.clinical-history.store');
        Route::put('/admin/pacientes/{pacienteId}/historia-clinica/{turnoId}', [App\Http\Controllers\ClinicalHistoryController::class, 'update'])->name('admin.clinical-history.update');
        Route::get('/admin/pacientes/{pacienteId}/historia-clinica/search', [App\Http\Controllers\ClinicalHistoryController::class, 'search'])->name('admin.clinical-history.search');
        Route::get('/admin/pacientes/{pacienteId}/historia-clinica/export-pdf', [App\Http\Controllers\ClinicalHistoryController::class, 'exportPdf'])->name('admin.clinical-history.export-pdf');


        Route::get('/admin/appointments', [AppointmentController::class, 'index'])->name('admin.appointments');
        Route::post('/admin/appointments/{id}/confirm', [AppointmentController::class, 'confirm'])->name('admin.appointments.confirm');
        Route::post('/admin/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('admin.appointments.cancel');
        Route::post('/admin/appointments/cancel-projected', [AppointmentController::class, 'cancelProjected'])->name('admin.appointments.cancelProjected');
        Route::post('/admin/appointments/store-recovery', [AppointmentController::class, 'storeRecovery'])->name('admin.appointments.storeRecovery');
        Route::post('/admin/payments/{id}/verify', [PaymentController::class, 'verify'])->name('admin.payments.verify');
        Route::post('/admin/payments/{id}/reject', [PaymentController::class, 'reject'])->name('admin.payments.reject');
        
        // Recursos (Biblioteca)
        Route::post('/admin/resources', [\App\Http\Controllers\ResourceController::class, 'store'])->name('admin.resources.store');
        Route::delete('/admin/resources/{id}', [\App\Http\Controllers\ResourceController::class, 'destroy'])->name('admin.resources.destroy');
        Route::get('/resources/{id}/download', [\App\Http\Controllers\ResourceController::class, 'download'])->name('resources.download');

        // Historial de Acciones
        Route::get('/admin/activity-logs', function() {
            return \App\Models\ActivityLog::with('user')->latest()->take(50)->get();
        })->name('admin.activity-logs');

        Route::post('/admin/activity-logs/{id}/revert', [\App\Http\Controllers\ActivityLogController::class, 'revert'])->name('admin.activity-logs.revert');

        Route::post('/admin/patients/{id}/update-type', [AuthController::class, 'updatePatientType'])->name('admin.patients.update-type');
        Route::post('/admin/patients/{id}/update-meet', [AuthController::class, 'updateMeetLink'])->name('admin.patients.update-meet');
        Route::post('/admin/patients/{id}/send-reminder', [AuthController::class, 'sendManualReminder'])->name('admin.patients.send-reminder');

        Route::delete('/admin/patients/{id}', [AuthController::class, 'destroyPatient'])->name('admin.patients.destroy');
        
        // Developer Dashboard
        // Developer Dashboard
        Route::get('/admin/developer', [App\Http\Controllers\DeveloperController::class, 'index'])->name('admin.developer');
        Route::post('/admin/developer/clear-cache', [App\Http\Controllers\DeveloperController::class, 'clearCache'])->name('admin.developer.clear-cache');
        Route::post('/admin/developer/maintenance-mode', [App\Http\Controllers\DeveloperController::class, 'toggleMaintenance'])->name('admin.developer.maintenance-mode');
        

        // Documents (Admin)
        Route::post('/admin/documents', [App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
        Route::delete('/admin/documents/{document}', [App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy');

        // Agenda
        Route::get('/admin/agenda', [App\Http\Controllers\DashboardController::class, 'adminAgenda'])->name('admin.agenda');
        Route::get('/admin/agenda/day-details', [App\Http\Controllers\DashboardController::class, 'getAgendaDayDetails'])->name('admin.agenda.day-details');

        // Waitlist (Admin)
        Route::delete('/admin/waitlist/{id}', [\App\Http\Controllers\WaitlistController::class, 'destroy'])->name('admin.waitlist.destroy');

        // Calendar Sync (Admin)
        Route::post('/admin/calendar/token', [\App\Http\Controllers\CalendarController::class, 'generateToken'])->name('admin.calendar.generateToken');
        Route::post('/admin/calendar/google-url', [\App\Http\Controllers\CalendarController::class, 'updateGoogleUrl'])->name('admin.calendar.google-url');
        Route::post('/admin/calendar/sync', [\App\Http\Controllers\CalendarController::class, 'syncGoogle'])->name('admin.calendar.sync');
        Route::post('/admin/calendar/sync-force', [\App\Http\Controllers\CalendarController::class, 'syncGoogleForce'])->name('admin.calendar.sync-force');
        Route::post('/admin/calendar/toggle-weekends', [\App\Http\Controllers\CalendarController::class, 'toggleWeekends'])->name('admin.calendar.toggle-weekends');
        
        
        // Availabilities (Admin)
        Route::post('/admin/availabilities', [\App\Http\Controllers\CalendarController::class, 'storeAvailability'])->name('admin.availabilities.store');
        Route::delete('/admin/availabilities/{id}', [\App\Http\Controllers\CalendarController::class, 'deleteAvailability'])->name('admin.availabilities.destroy');

        // Blocked Days (Admin)
        Route::post('/admin/blocked-days', [\App\Http\Controllers\CalendarController::class, 'storeBlockedDay'])->name('admin.blocked-days.store');
        Route::delete('/admin/blocked-days/{id}', [\App\Http\Controllers\CalendarController::class, 'destroyBlockedDay'])->name('admin.blocked-days.destroy');
        Route::post('/admin/calendar/import-holidays', [\App\Http\Controllers\CalendarController::class, 'importHolidays'])->name('admin.calendar.import-holidays');
        Route::post('/admin/settings', [\App\Http\Controllers\CalendarController::class, 'updateSettings'])->name('admin.settings.update');

        // Finanzas
        Route::get('/admin/finanzas', [\App\Http\Controllers\FinanceController::class, 'index'])->name('admin.finanzas');
        Route::get('/admin/finanzas/report', [\App\Http\Controllers\FinanceController::class, 'report'])->name('admin.finanzas.report');
        Route::get('/admin/finanzas/data', [\App\Http\Controllers\FinanceController::class, 'getChartData'])->name('admin.finance.chart-data');
        Route::post('/admin/finanzas/pricing', [\App\Http\Controllers\FinanceController::class, 'updatePrices'])->name('admin.finance.update-prices');
        Route::post('/admin/patients/{id}/update-honorario', [\App\Http\Controllers\FinanceController::class, 'updatePatientHonorario'])->name('admin.patients.update-honorario');
        
        // Gastos (Finance)
        Route::post('/admin/finanzas/gastos', [\App\Http\Controllers\FinanceController::class, 'storeExpense'])->name('admin.finanzas.store-expense');
        Route::delete('/admin/finanzas/gastos/{id}', [\App\Http\Controllers\FinanceController::class, 'destroyExpense'])->name('admin.finanzas.destroy-expense');
    });

    // Public iCal Feed (using token)
    Route::get('/agenda/feed/{token}', [\App\Http\Controllers\CalendarController::class, 'feed'])->name('agenda.feed');
});
