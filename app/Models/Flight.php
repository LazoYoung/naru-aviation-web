<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Database\Factories\FlightFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * App\Models\Flight
 *
 * @property int $id
 * @property int $user_id
 * @property string $refreshed_at
 * @property int $offline
 * @property int $status
 * @property string|null $off_block
 * @property string|null $on_block
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $altitude
 * @property int|null $airspeed
 * @property int|null $heading
 * @property-read User $user
 * @property-read Flightplan|null $flightplan
 * @method static FlightFactory factory($count = null, $state = [])
 * @method static Builder|Flight newModelQuery()
 * @method static Builder|Flight newQuery()
 * @method static Builder|Flight query()
 * @method static Builder|Flight whereAirspeed($value)
 * @method static Builder|Flight whereAltitude($value)
 * @method static Builder|Flight whereCreatedAt($value)
 * @method static Builder|Flight whereHeading($value)
 * @method static Builder|Flight whereId($value)
 * @method static Builder|Flight whereLatitude($value)
 * @method static Builder|Flight whereLongitude($value)
 * @method static Builder|Flight whereStatus($value)
 * @method static Builder|Flight whereUpdatedAt($value)
 * @method static Builder|Flight whereUserId($value)
 * @method static Builder|Flight whereOffline($value)
 * @method static Builder|Flight whereOffBlock($value)
 * @method static Builder|Flight whereOnBlock($value)
 * @method static Builder|Flight whereRefreshedAt($value)
 * @mixin Eloquent
 */
class Flight extends Model {
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function flightplan(): HasOne {
        return $this->hasOne(Flightplan::class);
    }

    /**
     * @return CarbonInterval the actual recorded flight time
     * @throws RuntimeException thrown if this flight has not finished yet
     */
    public function getFlightTime(): CarbonInterval {
        if (!isset($this->off_block) || !isset($this->on_block)) {
            throw new RuntimeException("Unable to calculate the flight time of an unfinished flight.");
        }

        $offBlock = Carbon::parse($this->off_block);
        $onBlock = Carbon::parse($this->on_block);
        return $onBlock->diffAsCarbonInterval($offBlock);
    }

    /**
     * @return CarbonInterval
     */
    public function getOfflineTime(): CarbonInterval {
        if ($this->offline) {
            return Carbon::now()->diffAsCarbonInterval($this->refreshed_at);
        } else {
            return CarbonInterval::seconds(0);
        }
    }

}
