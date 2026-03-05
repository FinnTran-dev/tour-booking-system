<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'Submitted';
    public const STATUS_CONFIRMED = 'Confirmed';
    public const STATUS_CANCELLED = 'Cancelled';

    protected $fillable = [
        'tour_id',
        'tour_date_id',
        'customer_name',
        'customer_email',
        'status',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function tourDate(): BelongsTo
    {
        return $this->belongsTo(TourDate::class);
    }

    public function passengers(): BelongsToMany
    {
        return $this->belongsToMany(Passenger::class, 'booking_passenger')
            ->withPivot('special_request')
            ->withTimestamps();
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
