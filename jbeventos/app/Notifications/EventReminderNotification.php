<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage; 
use App\Models\Event;
use Carbon\Carbon;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $timeBefore; // Ex: '24 horas', '1 hora'

    public function __construct(Event $event, string $timeBefore = 'em breve')
    {
        $this->event = $event;
        $this->timeBefore = $timeBefore;
    }

    /**
     * Define os canais de envio.
     * Adiciona 'broadcast' para o tempo real.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast']; // Adiciona 'broadcast'
    }

    /**
     * Constrói o email usando MailMessage.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $startDate = Carbon::parse($this->event->event_scheduled_at);
        $diff = Carbon::now()->locale('pt_BR')->diffForHumans($startDate, [
            'parts' => 2,
            'short' => false,
            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
        ]);

        return (new MailMessage)
            ->subject('Não se esqueça: ' . $this->event->event_name . ' começa em breve!')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('🚨 **LEMBRETE**: O evento que você segue está quase começando!')
            ->line('**' . $this->event->event_name . '**')
            ->line('**Local:** ' . $this->event->event_location)
            ->line('📅 **Data e hora:** ' . $startDate->format('d/m/Y H:i'))
            ->line('⏳ Faltam ' . $diff . ' para começar.')
            ->action('Ver detalhes do evento', route('events.show', $this->event->id))
            ->line('Esperamos você lá!');
    }
    
    /**
     * Obtém a representação da notificação para o canal "broadcast" (WebSockets).
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // Reutiliza o payload de dados do toArray()
        $data = $this->toArray($notifiable);
        
        return new BroadcastMessage([
            'data' => $data,
            // Adiciona a contagem de não lidas para que o frontend possa atualizar o sino
            'unread_count' => $notifiable->unreadNotifications()->count(), 
            'event_id' => $this->event->id,
        ]);
    }

    /**
     * Obtém a representação da notificação para o canal "database".
     */
    public function toArray(object $notifiable): array
    {
        $message = "⏳ O evento **{$this->event->event_name}** começa {$this->timeBefore}! Não se atrase.";
        
        return [
            'type' => 'event_reminder', // Identificador da notificação
            'event_id' => $this->event->id,
            'event_name' => $this->event->event_name,
            'event_url' => route('events.show', $this->event->id),
            'message' => $message,
            'reminder_time' => $this->timeBefore,
        ];
    }
}