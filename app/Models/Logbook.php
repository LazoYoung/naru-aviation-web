<?php

namespace App\Models;

use App\Services\Flight\Flight;
use Database\Factories\LogbookFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * App\Models\Logbook
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property string $origin
 * @property string $destination
 * @property string $date
 * @property int $flight_time
 * @property-read User $user
 * @method static LogbookFactory factory($count = null, $state = [])
 * @method static Builder|Logbook newModelQuery()
 * @method static Builder|Logbook newQuery()
 * @method static Builder|Logbook query()
 * @method static Builder|Logbook whereCreatedAt($value)
 * @method static Builder|Logbook whereDate($value)
 * @method static Builder|Logbook whereDestination($value)
 * @method static Builder|Logbook whereFlightTime($value)
 * @method static Builder|Logbook whereId($value)
 * @method static Builder|Logbook whereOrigin($value)
 * @method static Builder|Logbook whereUpdatedAt($value)
 * @method static Builder|Logbook whereUserId($value)
 * @mixin Eloquent
 */
class Logbook extends Model {
    use HasFactory;

    protected $guarded = [];

    /**
     * Create a {@link Logbook} out of this $flight
     * @throws RuntimeException thrown if the flightplan is not available
     */
    public static function create(Flight $flight): Logbook {
        $flightplan = $flight->getFlightplan();
        $logbook = new Logbook([
            "origin" => $flight->getOrigin(),
            "destination" => $flight->getDestination(),
            "date" => Carbon::parse($flightplan->getOffBlock()),
            "flight_time" => $flight->getFlightTime()->i
        ]);
        $user_id = $flight->getUserId();
        $user = User::whereId($user_id);

        if (!isset($user)) {
            throw new RuntimeException("User not found: $user_id");
        }

        $logbook->user()->associate($user);
        return $logbook;
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
