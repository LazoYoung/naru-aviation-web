<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @mixin \Eloquent
 */
class Post extends Model {
    use HasFactory;

    public mixed $content;
    protected $fillable = [
        'content'
    ];

    public function thread(): BelongsTo {
        return $this->belongsTo(Thread::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
