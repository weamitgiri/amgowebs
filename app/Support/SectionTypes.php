<?php

namespace App\Support;

class SectionTypes
{
    /**
     * Each type: label, icon, and a field schema.
     * Field: ['key' => ..., 'label' => ..., 'type' => text|textarea|html|url|select|repeater, ...]
     * Repeater fields carry a 'fields' sub-schema.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'hero' => [
                'label' => 'Hero Banner', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'highlight', 'label' => 'Highlight (gradient) text', 'type' => 'text'],
                    ['key' => 'subheading', 'label' => 'Subheading', 'type' => 'textarea'],
                    ['key' => 'background_image', 'label' => 'Background banner image URL (optional — upload in Media, paste URL)', 'type' => 'url'],
                    ['key' => 'primary_label', 'label' => 'Primary button text', 'type' => 'text'],
                    ['key' => 'primary_url', 'label' => 'Primary button URL', 'type' => 'url'],
                    ['key' => 'secondary_label', 'label' => 'Secondary button text', 'type' => 'text'],
                    ['key' => 'secondary_url', 'label' => 'Secondary button URL', 'type' => 'url'],
                ],
            ],
            'text_image' => [
                'label' => 'Text + Image', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'body', 'label' => 'Body (HTML)', 'type' => 'html'],
                    ['key' => 'image_url', 'label' => 'Image URL', 'type' => 'url'],
                    ['key' => 'align', 'label' => 'Image side', 'type' => 'select', 'options' => ['right' => 'Right', 'left' => 'Left']],
                ],
            ],
            'features' => [
                'label' => 'Features Grid', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'items', 'label' => 'Features', 'type' => 'repeater', 'fields' => [
                        ['key' => 'icon', 'label' => 'Icon', 'type' => 'text'],
                        ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
                        ['key' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                        ['key' => 'href', 'label' => 'Link URL (optional — makes the card clickable)', 'type' => 'url'],
                    ]],
                ],
            ],
            'stats' => [
                'label' => 'Statistics', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'items', 'label' => 'Stats', 'type' => 'repeater', 'fields' => [
                        ['key' => 'value', 'label' => 'Value', 'type' => 'text'],
                        ['key' => 'suffix', 'label' => 'Suffix', 'type' => 'text'],
                        ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                    ]],
                ],
            ],
            'faq' => [
                'label' => 'FAQ', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'items', 'label' => 'Questions', 'type' => 'repeater', 'fields' => [
                        ['key' => 'question', 'label' => 'Question', 'type' => 'text'],
                        ['key' => 'answer', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],
                ],
            ],
            'testimonials' => [
                'label' => 'Testimonials', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'items', 'label' => 'Testimonials', 'type' => 'repeater', 'fields' => [
                        ['key' => 'quote', 'label' => 'Quote', 'type' => 'textarea'],
                        ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
                        ['key' => 'role', 'label' => 'Role / Location', 'type' => 'text'],
                    ]],
                ],
            ],
            'cta' => [
                'label' => 'CTA Banner', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                    ['key' => 'button_label', 'label' => 'Button text', 'type' => 'text'],
                    ['key' => 'button_url', 'label' => 'Button URL', 'type' => 'url'],
                ],
            ],
            'pricing' => [
                'label' => 'Pricing Packages (live)', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                ],
            ],
            'blog_list' => [
                'label' => 'Latest Blog Posts (live)', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                ],
            ],
            'richtext' => [
                'label' => 'Rich Text / Content', 'fields' => [
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'body', 'label' => 'Body (HTML)', 'type' => 'html'],
                    ['key' => 'width', 'label' => 'Content width', 'type' => 'select', 'options' => ['narrow' => 'Narrow (articles, legal)', 'normal' => 'Normal']],
                    ['key' => 'tone', 'label' => 'Background', 'type' => 'select', 'options' => ['light' => 'Light', 'dark' => 'Dark']],
                ],
            ],
            'custom_html' => [
                'label' => 'Custom HTML', 'restricted' => true, 'fields' => [
                    ['key' => 'html', 'label' => 'HTML', 'type' => 'html'],
                ],
            ],
        ];
    }

    public static function labels(): array
    {
        return collect(self::all())->map(fn ($t) => $t['label'])->all();
    }

    public static function fields(string $type): array
    {
        return self::all()[$type]['fields'] ?? [];
    }

    public static function exists(string $type): bool
    {
        return isset(self::all()[$type]);
    }

    /** Default data for a new section of a type. */
    public static function defaults(string $type): array
    {
        $data = [];
        foreach (self::fields($type) as $field) {
            $data[$field['key']] = $field['type'] === 'repeater' ? [] : '';
        }

        return $data;
    }
}
