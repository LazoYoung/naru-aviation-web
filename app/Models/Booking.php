<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Booking
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property string $preflight_at
 * @property string $callsign
 * @property string $aircraft
 * @property string $origin
 * @property string|null $alternate
 * @property string $destination
 * @property int $altitude
 * @property string $off_block
 * @property string $on_block
 * @property string|null $route
 * @property string|null $remarks
 * @property-read User $user
 * @method static BookingFactory factory($count = null, $state = [])
 * @method static Builder|Booking newModelQuery()
 * @method static Builder|Booking newQuery()
 * @method static Builder|Booking query()
 * @method static Builder|Booking whereAircraft($value)
 * @method static Builder|Booking whereAlternate($value)
 * @method static Builder|Booking whereAltitude($value)
 * @method static Builder|Booking whereCallsign($value)
 * @method static Builder|Booking whereCreatedAt($value)
 * @method static Builder|Booking whereDestination($value)
 * @method static Builder|Booking whereId($value)
 * @method static Builder|Booking whereOffBlock($value)
 * @method static Builder|Booking whereOnBlock($value)
 * @method static Builder|Booking whereOrigin($value)
 * @method static Builder|Booking wherePreflightAt($value)
 * @method static Builder|Booking whereRemarks($value)
 * @method static Builder|Booking whereRoute($value)
 * @method static Builder|Booking whereUpdatedAt($value)
 * @method static Builder|Booking whereUserId($value)
 * @mixin Eloquent
 */
class Booking extends Model {
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
