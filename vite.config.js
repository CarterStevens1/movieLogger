import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// Custom plugin to minify inline scripts
function inlineScriptMinifier() {
    return {
        name: 'inline-script-minifier',
        transformIndexHtml: {
            order: 'post',
            handler(html) {
                // Only minify in production
                if (process.env.NODE_ENV === 'production') {
                    // Minify inline scripts
                    return html.replace(/<script(?![^>]*src)[^>]*>([\s\S]*?)<\/script>/gi, (match, scriptContent) => {
                        try {
                            // Simple minification - remove comments and extra whitespace
                            const minified = scriptContent
                                .replace(/\/\*[\s\S]*?\*\//g, '') // Remove block comments
                                .replace(/\/\/.*$/gm, '') // Remove line comments
                                .replace(/\s+/g, ' ') // Replace multiple spaces with single space
                                .replace(/;\s*}/g, ';}') // Remove space before closing braces
                                .replace(/{\s*/g, '{') // Remove space after opening braces
                                .trim();
                            
                            return match.replace(scriptContent, minified);
                        } catch (error) {
                            console.warn('Failed to minify inline script:', error);
                            return match;
                        }
                    });
                }
                return html;
            }
        }
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
          inlineScriptMinifier(), // Add the custom plugin
    ],
});

