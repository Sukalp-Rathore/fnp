<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FestivalEvent;
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Carbon\Carbon;

class SendEventRemainderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-remainder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to customers one day before the event date';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle()
    {
        // Get the current date and time
        $currentDate = Carbon::now();

        // Get events happening tomorrow
        $events = FestivalEvent::where('event_date', '=', $currentDate->addDay()->toDateString())->get();

        foreach ($events as $event) {
            // Fetch all customers
            $customers = Customer::all();

            foreach ($customers as $customer) {
                // Prepare email details
                $details = [
                    'subject' => 'Reminder: Upcoming Event',
                    'title' => 'Hello ' . $customer->customer_name,
                    'body' => 'This is a reminder for the upcoming event: ' . $event->events . ' happening on ' . $event->event_date . '.',
                ];

                // Determine the email template based on the festival name
                $template = $this->getTemplateForEvent($event->events);

                // Send email
                Mail::send($template, ['details' => $details], function ($message) use ($customer, $details) {
                    $message->to($customer->customer_email)
                            ->subject($details['subject']);
                });
            }
        }

        $this->info('Reminder emails sent successfully.');
        return 0;
    }

    /**
     * Get the email template for the given event name.
     *
     * @param string $eventName
     * @return string
     */
    private function getTemplateForEvent($eventName)
    {
        switch (strtolower($eventName)) {
            case 'christmas':
                return 'emails.christmas';
            case 'new year':
                return 'emails.new-year';
            default:
                return 'emails.default';
        }
    }
}

