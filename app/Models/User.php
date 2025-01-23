<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path',
        'broj_mobitela',
        'last_seen',  // Dodano za praćenje zadnje aktivnosti korisnika
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen' => 'datetime',  // Cast last_seen kao datetime
        ];
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'last_seen',  // Za korištenje diffForHumans()
    ];

    public function joinedRooms()
    {
        return $this->belongsToMany(Room::class, 'room_user')->withTimestamps();
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_user')->withPivot('created_at');
    }
    

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function isModerator()
    {
        return $this->role === 'moderator';
    }

    // Getter za sliku (ako nema slike, vrati default)
    public function profilePhotoUrl()
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : asset('images/default-profile.png');
    }

    public function givenRatings()
{
    return $this->hasMany(PlayerRating::class, 'rater_id');
}

public function receivedRatings()
{
    return $this->hasMany(PlayerRating::class, 'rated_id');
}

}

