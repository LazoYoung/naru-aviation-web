<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Booking
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $preflight_at
 * @property int $user_id
 * @property-read User $user
 * @property-read Flightplan|null $flightplan
 * @method static BookingFactory factory($count = null, $state = [])
 * @method static Builder|Booking newModelQuery()
 * @method static Builder|Booking newQuery()
 * @method static Builder|Booking query()
 * @method static Builder|Booking whereUserId($value)
 * @method static Builder|Booking whereCreatedAt($value)
 * @method static Builder|Booking whereId($value)
 * @method static Builder|Booking wherePreflightAt($value)
 * @method static Builder|Booking whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Booking extends Model {
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function flightplan(): HasOne {
        return $this->hasOne(Flightplan::class);
    }
}
