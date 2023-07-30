<?php

namespace App\Models;

use Eloquent;
use Database\Factories\FlightFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Flight
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property int $cancelled
 * @property string $aircraft
 * @property string $flight_number
 * @property string $sched_off_block
 * @property string $sched_on_block
 * @property string $origin
 * @property string|null $alternate
 * @property string $destination
 * @property int $altitude
 * @property string|null $route
 * @property string|null $remarks
 * @property-read Beacon|null $beacon
 * @method static FlightFactory factory($count = null, $state = [])
 * @method static Builder|Flight newModelQuery()
 * @method static Builder|Flight newQuery()
 * @method static Builder|Flight query()
 * @method static Builder|Flight whereAircraft($value)
 * @method static Builder|Flight whereAlternate($value)
 * @method static Builder|Flight whereAltitude($value)
 * @method static Builder|Flight whereCreatedAt($value)
 * @method static Builder|Flight whereDestination($value)
 * @method static Builder|Flight whereFlightNumber($value)
 * @method static Builder|Flight whereId($value)
 * @method static Builder|Flight whereOrigin($value)
 * @method static Builder|Flight whereRemarks($value)
 * @method static Builder|Flight whereRoute($value)
 * @method static Builder|Flight whereSchedOffBlock($value)
 * @method static Builder|Flight whereSchedOnBlock($value)
 * @method static Builder|Flight whereUpdatedAt($value)
 * @method static Builder|Flight whereUserId($value)
 * @method static Builder|Flight whereCancelled($value)
 * @mixin Eloquent
 */
class Flight extends Model {
    use HasFactory;

    private static int $max_delay = 30;
    protected $guarded = [];

    /**
     * @return Collection<Flight> the collection of flights that are overdue
     */
    public static function getStaleModels(): Collection {
        return Flight::query()
            ->whereNull("time_off_block")
            ->where("sched_off_block", "<", Carbon::now()->subMinutes(Flight::$max_delay))
            ->get();
    }

    public function beacon(): HasOne {
        return $this->hasOne(Beacon::class);
    }

}
