<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Ad;

class Kernel extends ConsoleKernel
{
    /**
     * Définir les tâches planifiées
     */
    protected function schedule(Schedule $schedule): void
    {
        // Exécuter ce bloc toutes les heures
        $schedule->call(function () {

            // Vérifie si l'auto-boost est activé dans config/boost.php
            if (config('boost.auto_boost')) {
                // 🚀 Cas 1 : Auto-boost activé
                // Si une annonce est expirée (boosted_until < maintenant),
                // on lui redonne un nouveau boost pour X heures
                Ad::where('boosted_until', '<', now())
                    ->update([
                        'boosted_until' => now()->addHours(config('boost.duration_hours'))
                    ]);

            } else {
                // ⚠️ Cas 2 : Auto-boost désactivé
                // Si une annonce est expirée, on retire son boost (null)
                // => Seuls les boosts payants restent actifs
                Ad::where('boosted_until', '<', now())
                    ->update([
                        'boosted_until' => null
                    ]);
            }

        })->hourly(); // Exécute ce job toutes les heures
    }

    /**
     * Charger les commandes Artisan
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
