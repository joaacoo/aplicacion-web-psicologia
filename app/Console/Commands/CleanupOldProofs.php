<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupOldProofs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-old-proofs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limitDate = now()->subDays(30);
        
        $oldPayments = \App\Models\Payment::where('created_at', '<', $limitDate)->get();
        
        $count = 0;
        foreach ($oldPayments as $payment) {
            if ($payment->comprobante_ruta && \Illuminate\Support\Facades\Storage::disk('local')->exists($payment->comprobante_ruta)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($payment->comprobante_ruta);
                // Opcionalmente borrar la referencia en DB o simplemente vaciar la ruta
                $payment->update(['comprobante_ruta' => null]);
                $count++;
            }
        }

        $this->info("Eliminados $count comprobantes antiguos (más de 30 días).");
        return 0;
    }
}
