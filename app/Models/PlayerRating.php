<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'rater_id',
        'rated_id',
        'room_id',
        'rating',
        'comment',
    ];

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }
    
    public function rated()
    {
        return $this->belongsTo(User::class, 'rated_id');
    }
    

    // Relacija prema terminu
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
