<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Thread
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property int $category
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|Thread newModelQuery()
 * @method static Builder|Thread newQuery()
 * @method static Builder|Thread query()
 * @method static Builder|Thread whereCategory($value)
 * @method static Builder|Thread whereContent($value)
 * @method static Builder|Thread whereCreatedAt($value)
 * @method static Builder|Thread whereId($value)
 * @method static Builder|Thread whereTitle($value)
 * @method static Builder|Thread whereUpdatedAt($value)
 * @method static Builder|Thread whereUserId($value)
 * @property-read Collection<int, Post> $posts
 * @property-read int|null $posts_count
 * @property int $view
 * @method static Builder|Thread whereView($value)
 * @mixin Eloquent
 */
class Thread extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
    ];

    public function posts(): HasMany {
        return $this->hasMany(Post::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
