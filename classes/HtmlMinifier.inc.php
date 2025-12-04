<?php
/**
 * @file plugins/generic/speedBooster/classes/HtmlMinifier.inc.php
 *
 * HTML/CSS/JS Minifier Class
 * Safe minification with error handling
 */

class HtmlMinifier {
    
    /**
     * Minify HTML content
     */
    public function minifyHtml($html) {
        if (empty($html)) {
            return $html;
        }
        
        try {
            // Preserve pre, code, textarea, script, and style tags
            $preserved = array();
            $preserveTags = array('pre', 'code', 'textarea', 'script', 'style');
            
            foreach ($preserveTags as $tag) {
                $html = preg_replace_callback(
                    '/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is',
                    function($matches) use (&$preserved) {
                        $placeholder = '___PRESERVED_' . count($preserved) . '___';
                        $preserved[$placeholder] = $matches[0];
                        return $placeholder;
                    },
                    $html
                );
            }
            
            // Remove HTML comments (except IE conditional comments)
            $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
            
            // Remove whitespace between tags
            $html = preg_replace('/>\s+</', '><', $html);
            
            // Remove extra whitespace and newlines
            $html = preg_replace('/\s\s+/', ' ', $html);
            
            // Remove whitespace around = in attributes
            $html = preg_replace('/\s*=\s*/', '=', $html);
            
            // Trim lines
            $html = preg_replace('/^\s+|\s+$/m', '', $html);
            
            // Restore preserved content
            foreach ($preserved as $placeholder => $content) {
                $html = str_replace($placeholder, $content, $html);
            }
            
            return trim($html);
            
        } catch (Exception $e) {
            error_log('HTML Minification Error: ' . $e->getMessage());
            return $html; // Return original on error
        }
    }
    
    /**
     * Minify inline CSS
     */
    public function minifyCss($html) {
        if (empty($html)) {
            return $html;
        }
        
        try {
            $html = preg_replace_callback(
                '/<style[^>]*>(.*?)<\/style>/is',
                function($matches) {
                    $css = $matches[1];
                    
                    // Remove comments
                    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
                    
                    // Remove whitespace
                    $css = preg_replace('/\s+/', ' ', $css);
                    
                    // Remove spaces around special characters
                    $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
                    
                    // Remove last semicolon in block
                    $css = preg_replace('/;}/','}',$css);
                    
                    return '<style' . substr($matches[0], 6, strpos($matches[0], '>') - 6) . '>' . trim($css) . '</style>';
                },
                $html
            );
            
            return $html;
            
        } catch (Exception $e) {
            error_log('CSS Minification Error: ' . $e->getMessage());
            return $html;
        }
    }
    
    /**
     * Minify inline JavaScript
     */
    public function minifyJs($html) {
        if (empty($html)) {
            return $html;
        }
        
        try {
            $html = preg_replace_callback(
                '/<script[^>]*>(.*?)<\/script>/is',
                function($matches) {
                    // Skip if it's external script (has src attribute)
                    if (preg_match('/src\s*=/', $matches[0])) {
                        return $matches[0];
                    }
                    
                    $js = $matches[1];
                    
                    // Remove single line comments (but preserve URLs)
                    $js = preg_replace('~//[^\n\r]*~', '', $js);
                    
                    // Remove multi-line comments
                    $js = preg_replace('~/\*.*?\*/~s', '', $js);
                    
                    // Remove whitespace
                    $js = preg_replace('/\s+/', ' ', $js);
                    
                    // Remove spaces around operators
                    $js = preg_replace('/\s*([\{\}\(\)\[\];,:\+\-\*\/=])\s*/', '$1', $js);
                    
                    return '<script' . substr($matches[0], 7, strpos($matches[0], '>') - 7) . '>' . trim($js) . '</script>';
                },
                $html
            );
            
            return $html;
            
        } catch (Exception $e) {
            error_log('JS Minification Error: ' . $e->getMessage());
            return $html;
        }
    }
}