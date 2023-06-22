<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportHasFailedNotification extends Notification
{
    use Queueable;

    protected $failures;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($failures)
    {
        $this->failures = $failures;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = new MailMessage();

        $message->error();
        $message->subject('Import Failed');
        $message->line('The following errors occurred while importing the Excel file:');

        foreach ($this->failures as $failure) {
            $error = implode(', ', $failure->errors());
            $message->line("At Row {$failure->row()}: {$error}");
        }

        return $message;

        /*return (new MailMessage)
                    ->error()
                    ->subject('Import Failed')
                    ->line('An error occurred during the import process.')
                    ->line('Error Message: '.$this->exception->getMessage());*/
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
