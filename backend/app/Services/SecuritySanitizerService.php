<?php

namespace App\Services;

class SecuritySanitizerService
{
    /**
     * Domain of the website to distinguish internal vs external links
     */
    protected static array $internalDomains = [
        'dichvuinnhatnguyen.vn',
        'www.dichvuinnhatnguyen.vn',
        'innhatnguyen.vn',
        'www.innhatnguyen.vn',
        'localhost',
        '127.0.0.1'
    ];

    /**
     * Sanitize rich text HTML content from outsourced writers.
     * Removes malicious scripts, iframes, inline event handlers, phishing vectors,
     * and enforces rel="nofollow noopener noreferrer" on external links to prevent SEO spam.
     */
    public static function sanitizeHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $clean = $html;

        // 1. Remove dangerous wrapper tags completely (tag + contents)
        $dangerousTags = [
            'script',
            'object',
            'embed',
            'applet',
            'meta',
            'link',
            'base',
            'form',
            'input',
            'button',
            'textarea',
            'select'
        ];

        foreach ($dangerousTags as $tag) {
            // Remove paired tags and their interior
            $clean = preg_replace("#<{$tag}\b[^>]*>.*?</{$tag}>#is", '', $clean);
            // Remove self-closing or lone tags
            $clean = preg_replace("#<{$tag}\b[^>]*\/?>#is", '', $clean);
        }

        // 2. Handle iframes: Allow only whitelisted video providers (YouTube, Vimeo), strip all others
        $clean = preg_replace_callback('#<iframe\b([^>]*)>(.*?)(?:</iframe>)?#is', function ($matches) {
            $attrs = $matches[1];
            // Check if src contains youtube or vimeo embed
            if (preg_match('#src=["\'](https?:\/\/(?:www\.)?(?:youtube\.com\/embed\/|player\.vimeo\.com\/video\/)[^"\']+)["\']#i', $attrs, $srcMatch)) {
                $safeSrc = htmlspecialchars($srcMatch[1], ENT_QUOTES, 'UTF-8');
                return '<iframe src="' . $safeSrc . '" frameborder="0" allowfullscreen class="w-full aspect-video rounded-xl my-4"></iframe>';
            }
            return ''; // Strip unauthorized iframes
        }, $clean);

        // 3. Remove inline event handlers (e.g. onload, onerror, onclick, onmouseover, onfocus, etc.)
        $clean = preg_replace('/\s+on[a-z]{3,20}\s*=\s*(?:["\'][^"\']*["\']|[^\s>]+)/i', '', $clean);

        // 4. Strip dangerous pseudo-protocols in attributes (javascript:, vbscript:, data:text/html)
        $clean = preg_replace('/(href|src|poster)\s*=\s*["\']\s*(?:javascript|vbscript|data:text\/html|livescript):[^"\']*["\']/i', '$1="#"', $clean);

        // 5. Clean style attributes: remove expressions, behavior, javascript in CSS
        $clean = preg_replace_callback('/style\s*=\s*(["\'])(.*?)\1/is', function ($matches) {
            $css = $matches[2];
            // Remove expressions, behavior, url(javascript:...)
            $css = preg_replace('/expression\s*\(.*?\)/is', '', $css);
            $css = preg_replace('/behavior\s*:.*?(?:;|$)/is', '', $css);
            $css = preg_replace('/url\s*\(\s*["\']?\s*(?:javascript|vbscript|data):.*?\)/is', '', $css);
            $cleanCss = htmlspecialchars(trim($css), ENT_QUOTES, 'UTF-8');
            return empty($cleanCss) ? '' : 'style="' . $cleanCss . '"';
        }, $clean);

        // 6. Anti-SEO-Spam & External Link Protection
        // Enforce rel="nofollow noopener noreferrer" and target="_blank" for external domains
        $clean = preg_replace_callback('/<a\b([^>]*)>/is', function ($matches) {
            $attrs = $matches[1];

            // Extract href
            if (!preg_match('/href\s*=\s*(["\'])(.*?)\1/is', $attrs, $hrefMatch)) {
                return $matches[0];
            }

            $url = trim($hrefMatch[2]);

            // If it's an internal or anchor link, keep it clean
            if (empty($url) || str_starts_with($url, '#') || str_starts_with($url, '/') || str_starts_with($url, 'tel:') || str_starts_with($url, 'mailto:')) {
                return $matches[0];
            }

            // Parse host
            $host = parse_url($url, PHP_URL_HOST);
            $isInternal = false;
            if ($host) {
                foreach (self::$internalDomains as $domain) {
                    if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                        $isInternal = true;
                        break;
                    }
                }
            }

            // If external link: enforce rel="nofollow noopener noreferrer" & target="_blank"
            if (!$isInternal) {
                // Remove existing rel and target attributes
                $attrs = preg_replace('/\s*rel\s*=\s*(["\']).*?\1/is', '', $attrs);
                $attrs = preg_replace('/\s*target\s*=\s*(["\']).*?\1/is', '', $attrs);
                // Append secure attributes
                $attrs .= ' rel="nofollow noopener noreferrer" target="_blank"';
            }

            return '<a ' . trim($attrs) . '>';
        }, $clean);

        return trim($clean);
    }

    /**
     * Extract list of external links found in the content for audit & review
     */
    public static function extractExternalLinks(?string $html): array
    {
        if (empty($html)) {
            return [];
        }

        $links = [];
        if (preg_match_all('/<a\b[^>]*href\s*=\s*(["\'])(https?:\/\/[^"\']+)\1/is', $html, $matches)) {
            foreach ($matches[2] as $url) {
                $host = parse_url($url, PHP_URL_HOST);
                $isInternal = false;
                if ($host) {
                    foreach (self::$internalDomains as $domain) {
                        if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                            $isInternal = true;
                            break;
                        }
                    }
                }
                if (!$isInternal && !in_array($url, $links)) {
                    $links[] = $url;
                }
            }
        }

        return $links;
    }

    /**
     * Clean plain text input (e.g. title, excerpt, meta tags)
     */
    public static function sanitizeText(?string $text): string
    {
        if (empty($text)) {
            return '';
        }
        return trim(strip_tags($text));
    }
}

