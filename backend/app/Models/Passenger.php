<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Passenger extends Model
{
    use HasFactory;

    public const STATUS_ENABLED = 'Enabled';
    public const STATUS_DISABLED = 'Disabled';

    protected $fillable = [
        'given_name',
        'surname',
        'email',
        'phone',
        'date_of_birth',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_passenger')
            ->withPivot('special_request')
            ->withTimestamps();
    }
}
