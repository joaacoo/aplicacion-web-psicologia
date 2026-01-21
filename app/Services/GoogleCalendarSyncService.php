<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExternalEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleCalendarSyncService
{
    /**
     * Sync external events from the user's Google Calendar iCal URL.
     */
    public function sync(User $user)
    {
        $pro = $user->profesional;
        if (!$pro || !$pro->google_calendar_url) {
            return false;
        }

        try {
            $response = Http::withoutVerifying()->get($pro->google_calendar_url);
            if (!$response->successful()) {
                Log::error("Failed to fetch Google Calendar for user {$user->id}");
                return false;
            }

            $icalContent = $response->body();
            $events = $this->parseIcal($icalContent);

            // Clear old external events for this user
            ExternalEvent::where('user_id', $user->id)->delete();

            // Insert new events
            foreach ($events as $event) {
                ExternalEvent::create([
                    'user_id' => $user->id,
                    'title' => $event['summary'] ?? 'Evento de Google',
                    'start_time' => $event['start'],
                    'end_time' => $event['end'],
                    'external_id' => $event['uid'] ?? null,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error syncing Google Calendar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Simple iCal parser for VEVENT components.
     */
    private function parseIcal($content)
    {
        $events = [];
        // Split by BEGIN:VEVENT
        $blocks = explode('BEGIN:VEVENT', $content);
        array_shift($blocks); // Remove header part

        foreach ($blocks as $block) {
            $block = explode('END:VEVENT', $block)[0];
            $lines = explode("\r\n", $block);
            if (count($lines) <= 1) $lines = explode("\n", $block);

            $event = [];
            foreach ($lines as $line) {
                if (strpos($line, ':') === false) continue;
                list($key, $value) = explode(':', $line, 2);
                
                // Handle parameters like DTSTART;VALUE=DATE:20240101
                $cleanKey = explode(';', $key)[0];

                if ($cleanKey === 'SUMMARY') $event['summary'] = $value;
                if ($cleanKey === 'UID') $event['uid'] = $value;
                if ($cleanKey === 'DTSTART') $event['start'] = $this->parseIcalDate($value);
                if ($cleanKey === 'DTEND') $event['end'] = $this->parseIcalDate($value);
            }

            if (isset($event['start']) && isset($event['end'])) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     * Parse iCal date format (e.g., 20240101T120000Z or 20240101)
     */
    private function parseIcalDate($dateStr)
    {
        try {
            // Remove any trailing Z or parameters
            $dateStr = trim($dateStr);
            
            if (strlen($dateStr) === 8) {
                // YYYYMMDD
                return Carbon::createFromFormat('Ymd', $dateStr)->startOfDay();
            }

            // ISO 8601 variations
            // We explicitly convert to the application's timezone so that UTC 'Z' dates 
            // are shifted correctly (e.g., 15:00Z -> 12:00 Argentina)
            return Carbon::parse($dateStr)->timezone(config('app.timezone'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
