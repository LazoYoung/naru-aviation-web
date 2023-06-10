<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Image
 *
 * @property-read User $user
 * @method static Builder|Image newModelQuery()
 * @method static Builder|Image newQuery()
 * @method static Builder|Image query()
 * @property int $id
 * @property int $user_id
 * @property string $file
 * @property string $author
 * @property string|null $title
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Image whereCreatedAt($value)
 * @method static Builder|Image whereDescription($value)
 * @method static Builder|Image whereId($value)
 * @method static Builder|Image whereTitle($value)
 * @method static Builder|Image whereUpdatedAt($value)
 * @method static Builder|Image whereUserId($value)
 * @method static Builder|Image whereFile($value)
 * @method static Builder|Image whereAuthor($value)
 * @mixin Eloquent
 */
class Image extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file',
        'author',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
