<?php

namespace App\Http\Controllers;

use App\Models\ContactFormSetting;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageSlide;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Mews\Purifier\Facades\Purifier;

class PageController extends Controller
{
    public function contact(): Response
    {
        return Inertia::render('contact', [
            'menuItems' => $this->getMenuItems(),
            'contactForm' => $this->getContactFormConfig(),
        ]);
    }

    public function home(): Response
    {
        $page = Page::query()
            ->with('slides')
            ->where('is_homepage', true)
            ->where('is_published', true)
            ->first();

        if (! $page) {
            return Inertia::render('pages/show', [
                'page' => [
                    'id' => 0,
                    'title' => 'Welcome',
                    'description' => null,
                    'slug' => '',
                    'hero' => [
                        'title' => null,
                        'text' => null,
                        'image_url' => null,
                        'button_label' => null,
                        'button_url' => null,
                    ],
                    'content' => '',
                    'slides' => [],
                ],
                'menuItems' => $this->getMenuItems(),
                'fallbackText' => 'There is no homepage set. Please create a page and set it as the homepage in the admin panel.',
            ]);
        }

        return Inertia::render('pages/show', [
            'page' => $this->transformPage($page),
            'menuItems' => $this->getMenuItems(),
        ]);
    }

    public function show(string $slug): Response
    {
        $page = Page::query()
            ->with('slides')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return Inertia::render('pages/show', [
            'page' => $this->transformPage($page),
            'menuItems' => $this->getMenuItems(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function transformPage(Page $page): array
    {
        return [
            'id' => $page->id,
            'title' => $page->title,
            'description' => $page->description,
            'slug' => $page->slug,
            'hero' => [
                'title' => $page->hero_title,
                'text' => $page->hero_text,
                'image_url' => $page->hero_image_path
                    ? Storage::url($page->hero_image_path)
                    : null,
                'button_label' => $page->hero_button_label,
                'button_url' => $page->hero_button_url,
            ],
            'content' => Purifier::clean($page->content ?? ''),
            'slides' => $page->slides->map(fn (PageSlide $slide): array => [
                'id' => $slide->id,
                'image_url' => Storage::url($slide->image_path),
                'caption' => $slide->caption,
                'alt_text' => $slide->alt_text,
            ])->values()->all(),
        ];
    }

    /**
     * @return array<int, array{id: int, label: string, url: string, open_in_new_tab: bool}>
     */
    protected function getMenuItems(): array
    {
        return MenuItem::query()
            ->with('page:id,slug,is_homepage')
            ->where('is_visible', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->map(fn (MenuItem $item) => [
                'id' => $item->id,
                'label' => $item->label,
                'url' => $this->resolveMenuItemUrl($item),
                'open_in_new_tab' => (bool) $item->open_in_new_tab,
            ])
            ->filter(fn (array $item) => filled($item['url']))
            ->values()
            ->all();
    }

    protected function resolveMenuItemUrl(MenuItem $item): ?string
    {
        if ($item->page) {
            return $item->page->is_homepage ? '/' : '/'.$item->page->slug;
        }

        if (filled($item->named_route) && Route::has($item->named_route)) {
            return route($item->named_route);
        }

        if (! filled($item->url)) {
            return null;
        }

        $url = trim($item->url);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return '/'.$url;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getContactFormConfig(): array
    {
        $settings = ContactFormSetting::query()->first();

        $defaultFields = [
            [
                'key' => 'name',
                'label' => $settings ? ($settings->name_label ?? 'Your name') : 'Your name',
                'type' => 'text',
                'required' => true,
                'placeholder' => '',
            ],
            [
                'key' => 'email',
                'label' => $settings ? ($settings->email_label ?? 'Email address') : 'Email address',
                'type' => 'email',
                'required' => true,
                'placeholder' => '',
            ],
            [
                'key' => 'message',
                'label' => $settings ? ($settings->message_label ?? 'Message') : 'Message',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => '',
            ],
        ];

        $rawFields = is_array($settings?->fields) ? $settings->fields : [];

        /** @var array<int, array<string, mixed>> $normalizedFields */
        $normalizedFields = collect($rawFields)->values()->all();

        $configuredFields = collect($normalizedFields)
            ->filter(
                fn (array $field): bool => filled($field['key'] ?? null) && filled($field['label'] ?? null),
            )
            ->map(fn (array $field): array => [
                'key' => (string) $field['key'],
                'label' => (string) $field['label'],
                'type' => in_array($field['type'] ?? '', ['text', 'email', 'textarea', 'tel'], true)
                    ? (string) $field['type']
                    : 'text',
                'required' => (bool) ($field['required'] ?? false),
                'placeholder' => (string) ($field['placeholder'] ?? ''),
            ])
            ->values()
            ->all();

        return [
            'title' => $settings ? $settings->title : 'Contact us',
            'intro_text' => $settings ? ($settings->intro_text ?? 'Have a question? Send us a message and we will reply soon.') : 'Have a question? Send us a message and we will reply soon.',
            'fields' => filled($configuredFields) ? $configuredFields : $defaultFields,
            'button_label' => $settings ? $settings->button_label : 'Send message',
            'success_toast' => $settings ? $settings->success_toast : 'Your message has been sent successfully.',
            'error_toast' => $settings ? $settings->error_toast : 'Something went wrong while sending your message.',
        ];
    }
}
