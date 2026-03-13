<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $slug
 * @property string|null $content
 * @property bool $is_homepage
 * @property bool $is_published
 * @property string|null $hero_title
 * @property string|null $hero_text
 * @property string|null $hero_image_path
 * @property string|null $hero_button_label
 * @property string|null $hero_button_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PageSlide> $slides
 */
class Page extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'hero_title',
        'hero_text',
        'hero_image_path',
        'hero_button_label',
        'hero_button_url',
        'slug',
        'content',
        'is_homepage',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_homepage' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return HasMany<PageSlide, $this>
     */
    public function slides(): HasMany
    {
        return $this->hasMany(PageSlide::class)->orderBy('sort');
    }

    protected static function booted(): void
    {
        static::saving(function (Page $page): void {
            if (! $page->is_homepage) {
                return;
            }

            static::query()
                ->when($page->exists, fn ($query) => $query->whereKeyNot($page->getKey()))
                ->update(['is_homepage' => false]);
        });
    }
}
