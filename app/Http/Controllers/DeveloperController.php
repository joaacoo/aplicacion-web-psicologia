<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ticket;
use App\Models\SystemLog;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class DeveloperController extends Controller
{
    // 1. Dashboard View
    public function index()
    {
        // A. Health Semaphore Logic
        $openCriticalTickets = Ticket::where('status', '!=', 'resuelto')
            ->where('priority', 'critica')
            ->count();
            
        $recentErrors = SystemLog::where('level', 'error')
            ->where('created_at', '>=', now()->subHour())
            ->count();

        $systemStatus = 'green';
        if ($openCriticalTickets > 0 || $recentErrors > 10) {
            $systemStatus = 'red';
        } elseif (Ticket::where('status', '!=', 'resuelto')->count() > 5) {
            $systemStatus = 'yellow';
        }

        // B. Metrics
        $totalPatients = User::where('rol', 'paciente')->count();
        $appointmentsToday = Appointment::whereDate('fecha_hora', now())->count();
        $appointmentsYesterday = Appointment::whereDate('fecha_hora', now()->subDay())->count();
        
        // Error Rate (Users with reports / Active Users)
        $usersWithIssues = Ticket::distinct('user_id')->count();
        $errorRate = $totalPatients > 0 ? round(($usersWithIssues / $totalPatients) * 100, 1) : 0;

        // C. Ticket List
        $tickets = Ticket::with('user')
            ->orderByRaw("FIELD(priority, 'critica', 'alta', 'media', 'baja')")
            ->orderBy('created_at', 'desc')
            ->get();

        // D. Latest Logs
        $logs = SystemLog::orderBy('created_at', 'desc')->take(20)->get();

        return view('dashboard.admin.developer', compact
            ('systemStatus', 'totalPatients', 'appointmentsToday', 'appointmentsYesterday', 'errorRate', 'tickets', 'logs')
        );
    }

    // 2. Store Ticket (from User)
    public function storeTicket(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'metadata' => 'nullable|array' // Snapshot data
        ]);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'subject' => 'Reporte de Usuario: ' . mb_strimwidth($request->description, 0, 30, '...'),
            'description' => $request->description,
            'status' => 'nuevo',
            'priority' => 'media', // Default, admin can escalate
            'metadata' => $request->metadata
        ]);

        // Send Email Notification to Admin
        try {
            // Replace with actual dev email, potentially from config
            \Illuminate\Support\Facades\Mail::to('joacooodelucaaa16@gmail.com')->queue(new \App\Mail\TicketAlert($ticket));
        } catch (\Exception $e) {
            // Log dont fail request
            \Illuminate\Support\Facades\Log::error('Failed to send ticket email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Reporte enviado correctamente. ID: #' . $ticket->id], 200);
    }

    // 3. Log Error (from Frontend JS)
    public function logError(Request $request)
    {
        SystemLog::create([
            'level' => 'error', // Asumimos error si viene del frontend handler
            'message' => $request->input('message', 'Unknown Error'),
            'context' => [
                'stack' => $request->input('stack'),
                'component' => $request->input('component'),
                'ua' => $request->header('User-Agent')
            ],
            'user_id' => auth()->id(),
            'url' => $request->input('url'),
            'ip' => $request->ip()
        ]);

        return response()->json(['status' => 'logged']);
    }
}
