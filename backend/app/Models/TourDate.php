<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourDate extends Model
{
    use HasFactory;

    public const STATUS_ENABLED = 'Enabled';
    public const STATUS_DISABLED = 'Disabled';

    protected $fillable = [
        'tour_id',
        'date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
