<?php

namespace App\Models;

use Database\Factories\FlightFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Flight
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property int $status
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
 * @mixin Eloquent
 */
class Flight extends Model {
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function flightplan(): HasOne {
        return $this->hasOne(Flightplan::class);
    }

}
