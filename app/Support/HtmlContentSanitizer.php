<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use RuntimeException;

class HtmlContentSanitizer
{
    private const ALLOWED_TAGS = [
        'a', 'b', 'blockquote', 'br', 'code', 'del', 'div', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'hr', 'i', 'li', 'ol', 'p', 'pre', 's', 'span', 'strong', 'sub', 'sup', 'table', 'tbody', 'td',
        'tfoot', 'th', 'thead', 'tr', 'u', 'ul',
    ];

    private const REMOVED_TAGS = ['embed', 'iframe', 'math', 'object', 'script', 'style', 'svg'];

    public static function sanitize(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrorSetting = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="content-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorSetting);

        if (! $loaded) {
            throw new RuntimeException('Konten HTML tidak dapat diproses.');
        }

        $root = $document->getElementById('content-root');
        if (! $root) {
            throw new RuntimeException('Konten HTML tidak dapat diproses.');
        }

        self::sanitizeChildren($root);

        $content = '';
        foreach ($root->childNodes as $child) {
            $content .= $document->saveHTML($child);
        }

        return $content;
    }

    private static function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                if ($child->nodeType !== XML_TEXT_NODE) {
                    $parent->removeChild($child);
                }

                continue;
            }

            $tag = strtolower($child->tagName);
            if (in_array($tag, self::REMOVED_TAGS, true)) {
                $parent->removeChild($child);

                continue;
            }

            self::sanitizeChildren($child);

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                while ($child->firstChild) {
                    $parent->insertBefore($child->firstChild, $child);
                }
                $parent->removeChild($child);

                continue;
            }

            self::sanitizeAttributes($child, $tag);
        }
    }

    private static function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $href = $tag === 'a' ? trim($element->getAttribute('href')) : '';
        $title = $element->getAttribute('title');

        while ($element->attributes->length > 0) {
            $element->removeAttributeNode($element->attributes->item(0));
        }

        if ($tag === 'a' && $href !== '' && self::isSafeLink($href)) {
            $element->setAttribute('href', $href);
        }

        if ($title !== '') {
            $element->setAttribute('title', $title);
        }
    }

    private static function isSafeLink(string $href): bool
    {
        if (preg_match('/^(?:\/\/|\\\\\\\\)/', $href)) {
            return false;
        }

        $scheme = parse_url($href, PHP_URL_SCHEME);

        return $scheme === null || in_array(strtolower($scheme), ['http', 'https', 'mailto'], true);
    }
}
