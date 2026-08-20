<?php

namespace App\Support;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><br><h2><h3><h4><strong><b><em><i><u><ul><ol><li><a><blockquote><span><div>';

    /**
     * Strips tags outside a formatting allow-list plus event-handler attributes
     * and javascript: URIs, since this HTML comes from a contenteditable editor
     * and is later rendered unescaped on the public school site.
     */
    public static function clean(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        $html = strip_tags($html, self::ALLOWED_TAGS);
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href|src)(\s*=\s*)(["\'])\s*javascript:[^"\']*\3/i', '$1$2$3#$3', $html);

        return $html;
    }
}
