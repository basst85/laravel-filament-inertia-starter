<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $label
 * @property int|null $page_id
 * @property string|null $named_route
 * @property string|null $url
 * @property int $sort
 * @property bool $is_visible
 * @property bool $open_in_new_tab
 * @property-read Page|null $page
 */
class MenuItem extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $fillable = [
        'label',
        'page_id',
        'named_route',
        'url',
        'sort',
        'is_visible',
        'open_in_new_tab',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'open_in_new_tab' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
