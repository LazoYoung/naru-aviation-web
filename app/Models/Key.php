<?php

namespace App\Models;

use Database\Factories\KeyFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Key
 *
 * @property-read User $user
 * @method static KeyFactory factory($count = null, $state = [])
 * @method static Builder|Key newModelQuery()
 * @method static Builder|Key newQuery()
 * @method static Builder|Key query()
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property string $type
 * @property string $value
 * @method static Builder|Key whereCreatedAt($value)
 * @method static Builder|Key whereId($value)
 * @method static Builder|Key whereType($value)
 * @method static Builder|Key whereUpdatedAt($value)
 * @method static Builder|Key whereUserId($value)
 * @method static Builder|Key whereValue($value)
 * @mixin Eloquent
 */
class Key extends Model {
    use HasFactory;

    protected $hidden = [
        'value'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
