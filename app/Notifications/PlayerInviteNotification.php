<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class PlayerInviteNotification extends Notification
{
    use Queueable;

    protected $room;
    protected $inviter;

    public function __construct($room, $inviter)
{
    $this->room = $room;
    $this->inviter = $inviter;
}


    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
{
    return [
        'message' => "{$this->inviter->name} te je pozvao da se pridružiš terminu: {$this->room->title}",
        'room_id' => $this->room->id,
        'inviter_name' => $this->inviter->name,
        'inviter_photo' => $this->inviter->profile_photo_path ?? '/default-avatar.jpg',
    ];
}


    
}
