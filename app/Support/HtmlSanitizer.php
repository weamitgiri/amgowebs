<?php

namespace App\Support;

use DOMDocument;
use DOMElement;

class HtmlSanitizer
{
    /** Tags allowed in admin rich-text content. */
    private const ALLOWED_TAGS = [
        'p', 'br', 'hr', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'b', 'em', 'i', 'u',
        's', 'a', 'ul', 'ol', 'li', 'blockquote', 'img', 'figure', 'figcaption', 'span', 'div',
        'table', 'thead', 'tbody', 'tr', 'td', 'th', 'code', 'pre',
    ];

    /** Attributes allowed on any element. */
    private const ALLOWED_ATTRS = ['href', 'src', 'alt', 'title', 'class', 'target', 'rel', 'colspan', 'rowspan'];

    public static function clean(?string $html): string
    {
        $html = (string) $html;
        if (trim($html) === '') {
            return '';
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div id="__root">'.$html.'</div>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $root = $dom->getElementById('__root');
        if ($root) {
            self::sanitizeNode($root);
        }

        $out = '';
        if ($root) {
            foreach (iterator_to_array($root->childNodes) as $child) {
                $out .= $dom->saveHTML($child);
            }
        }

        return trim($out);
    }

    private static function sanitizeNode(DOMElement $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->nodeName);

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                // Drop disallowed element but keep its (sanitized) text children.
                self::sanitizeNode($child);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);

                continue;
            }

            // Strip disallowed / dangerous attributes.
            foreach (iterator_to_array($child->attributes ?? []) as $attr) {
                $name = strtolower($attr->nodeName);
                $value = trim($attr->nodeValue);

                $isDangerous = str_starts_with($name, 'on')
                    || ! in_array($name, self::ALLOWED_ATTRS, true)
                    || (in_array($name, ['href', 'src'], true) && preg_match('/^\s*(javascript|data|vbscript):/i', $value));

                if ($isDangerous) {
                    $child->removeAttribute($attr->nodeName);
                }
            }

            // Force safe rel on external links.
            if ($tag === 'a' && $child->getAttribute('target') === '_blank') {
                $child->setAttribute('rel', 'noopener noreferrer');
            }

            self::sanitizeNode($child);
        }
    }
}
