<?php

namespace App\Models;

use Database\Factories\FlightplanFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Flightplan
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $booking_id
 * @property int $flight_id
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
 * @property-read Flight $flight
 * @property-read Booking $booking
 * @method static FlightplanFactory factory($count = null, $state = [])
 * @method static Builder|Flightplan newModelQuery()
 * @method static Builder|Flightplan newQuery()
 * @method static Builder|Flightplan query()
 * @method static Builder|Flightplan whereAircraft($value)
 * @method static Builder|Flightplan whereAlternate($value)
 * @method static Builder|Flightplan whereAltitude($value)
 * @method static Builder|Flightplan whereBookingId($value)
 * @method static Builder|Flightplan whereCallsign($value)
 * @method static Builder|Flightplan whereCreatedAt($value)
 * @method static Builder|Flightplan whereDestination($value)
 * @method static Builder|Flightplan whereFlightId($value)
 * @method static Builder|Flightplan whereId($value)
 * @method static Builder|Flightplan whereOffBlock($value)
 * @method static Builder|Flightplan whereOnBlock($value)
 * @method static Builder|Flightplan whereOrigin($value)
 * @method static Builder|Flightplan whereRemarks($value)
 * @method static Builder|Flightplan whereRoute($value)
 * @method static Builder|Flightplan whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Flightplan extends Model {
    use HasFactory;

    protected $guarded = [];

    public function booking(): BelongsTo {
        return $this->belongsTo(Booking::class);
    }

    public function flight(): BelongsTo {
        return $this->belongsTo(Flight::class);
    }
}
