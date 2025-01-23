<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location_id',
        'city_id',
        'date',
        'start_time',
        'end_time',
        'max_players',
        'owner_id',
        'password',
    ];
    

    /**
     * Relacija: Room pripada korisniku koji ga je kreirao.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relacija: Room pripada određenoj lokaciji.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Relacija: Korisnici (players) pridruženi sobi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function players()
{
    return $this->belongsToMany(User::class, 'room_user')->withTimestamps();
}

    /**
     * Relacija: Poruke u sobi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
