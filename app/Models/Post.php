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
 * App\Models\Post
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @property int $thread_id
 * @property int $user_id
 * @property string $content
 * @property int $like
 * @property-read Collection<int, Like> $likes
 * @property-read int|null $likes_count
 * @property-read Thread $thread
 * @property-read User $user
 * @method static Builder|Post whereContent($value)
 * @method static Builder|Post whereLike($value)
 * @method static Builder|Post whereThreadId($value)
 * @method static Builder|Post whereUserId($value)
 * @mixin Eloquent
 */
class Post extends Model {
    use HasFactory;

    protected $touches = [
        'thread'
    ];
    protected $fillable = [
        'content'
    ];

    public function thread(): BelongsTo {
        return $this->belongsTo(Thread::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany {
        return $this->hasMany(Like::class);
    }
}
