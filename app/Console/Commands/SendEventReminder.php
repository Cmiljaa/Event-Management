<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendEventReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notifications to all event attendees that event starts soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = \App\Models\Event::with('attendees')
        ->whereBetween('start_time', [now(), now()->addDay()])->get();

        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);

        foreach($events as $event){
            foreach ($event->attendees as $attendee){
                $this->info("Sent notification to {$attendee->user->id}");
            }
        }

        $this->info("Found {$eventCount} {$eventLabel}.");
        $this->info("Reminder notifications sent successfully!");
    }
}
