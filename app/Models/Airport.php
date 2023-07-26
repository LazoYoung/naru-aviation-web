<?php

namespace App\Models;

use Database\Factories\AirportFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Airport
 *
 * @property int $id
 * @property string $name
 * @property string $icao
 * @property string $latitude
 * @property string $longitude
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Airport newModelQuery()
 * @method static Builder|Airport newQuery()
 * @method static Builder|Airport query()
 * @method static Builder|Airport whereCreatedAt($value)
 * @method static Builder|Airport whereIcao($value)
 * @method static Builder|Airport whereId($value)
 * @method static Builder|Airport whereLatitude($value)
 * @method static Builder|Airport whereLongitude($value)
 * @method static Builder|Airport whereName($value)
 * @method static Builder|Airport whereUpdatedAt($value)
 * @method static AirportFactory factory($count = null, $state = [])
 * @mixin Eloquent
 */
class Airport extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'icao',
        'latitude',
        'longitude',
    ];
}
