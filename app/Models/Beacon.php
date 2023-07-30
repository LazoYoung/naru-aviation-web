<?php

namespace App\Models;

use Eloquent;
use Database\Factories\BeaconFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Beacon
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $flight_id
 * @property int $status
 * @property float $latitude
 * @property float $longitude
 * @property int $altitude
 * @property int $airspeed
 * @property int $heading
 * @property-read Flight $flight
 * @method static BeaconFactory factory($count = null, $state = [])
 * @method static Builder|Beacon newModelQuery()
 * @method static Builder|Beacon newQuery()
 * @method static Builder|Beacon query()
 * @method static Builder|Beacon whereAirspeed($value)
 * @method static Builder|Beacon whereAltitude($value)
 * @method static Builder|Beacon whereCreatedAt($value)
 * @method static Builder|Beacon whereFlightId($value)
 * @method static Builder|Beacon whereHeading($value)
 * @method static Builder|Beacon whereId($value)
 * @method static Builder|Beacon whereLatitude($value)
 * @method static Builder|Beacon whereLongitude($value)
 * @method static Builder|Beacon whereStatus($value)
 * @method static Builder|Beacon whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Beacon extends Model {
    use HasFactory;

    public function flight(): BelongsTo {
        return $this->belongsTo(Flight::class);
    }
}
