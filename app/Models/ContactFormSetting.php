<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string|null $intro_text
 * @property array<int, array<string, mixed>>|null $fields
 * @property string|null $name_label
 * @property string|null $email_label
 * @property string|null $message_label
 * @property string $button_label
 * @property string $success_toast
 * @property string $error_toast
 */
class ContactFormSetting extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $fillable = [
        'title',
        'intro_text',
        'fields',
        'name_label',
        'email_label',
        'message_label',
        'button_label',
        'success_toast',
        'error_toast',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
        ];
    }
}
