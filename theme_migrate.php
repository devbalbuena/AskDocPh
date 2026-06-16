<?php

$viewsDir = __DIR__ . '/resources/views';

$replacements = [
    // Backgrounds & Sections
    '/bg-gray-900/' => 'bg-gray-50',
    '/bg-gray-950/' => 'bg-white',
    '/bg-gray-800/' => 'bg-white',
    '/bg-gray-700\/50/' => 'bg-gray-50',
    '/bg-gray-700\/30/' => 'bg-gray-50',
    '/bg-gray-900\/60/' => 'bg-gray-50',
    '/bg-gray-900\/40/' => 'bg-gray-50',
    '/bg-gray-900\/30/' => 'bg-gray-50',
    
    // Text colors
    '/text-white/' => 'text-gray-900',
    '/text-gray-200/' => 'text-gray-700',
    '/text-gray-300/' => 'text-gray-700',
    '/text-gray-400/' => 'text-gray-500',
    
    // Borders
    '/border-gray-800/' => 'border-gray-200',
    '/border-gray-700/' => 'border-gray-200',
    '/border-gray-600/' => 'border-gray-300',

    // Purple to Green
    '/bg-purple-600\/30/' => 'bg-green-100',
    '/bg-purple-600\/20/' => 'bg-green-100',
    '/bg-purple-600\/10/' => 'bg-green-50',
    '/bg-purple-500\/20/' => 'bg-green-100',
    '/bg-purple-700\/40/' => 'bg-green-100',
    '/bg-purple-600/' => 'bg-green-600',
    '/bg-purple-700/' => 'bg-green-700',
    '/text-purple-300/' => 'text-green-700',
    '/text-purple-400/' => 'text-green-600',
    '/border-purple-700\/30/' => 'border-green-200',
    '/border-purple-600\/50/' => 'border-green-300',
    '/border-purple-500\/30/' => 'border-green-200',
    '/border-purple-500/' => 'border-green-500',
    '/focus:border-purple-500/' => 'focus:ring-green-500 focus:border-green-500',

    // Cards / Panels
    '/rounded-xl p-5/' => 'rounded-xl shadow-sm border border-gray-100 p-5',
    '/rounded-2xl p-6/' => 'rounded-2xl shadow-sm border border-gray-100 p-6',
    '/rounded-2xl p-5/' => 'rounded-2xl shadow-sm border border-gray-100 p-5',
    '/rounded-xl overflow-hidden/' => 'rounded-xl shadow-sm border border-gray-100 overflow-hidden',
    
    // Specific Badges
    '/bg-yellow-500\/20\s+text-yellow-400/' => 'bg-yellow-100 text-yellow-700',
    '/bg-green-500\/20\s+text-green-400/' => 'bg-green-100 text-green-700',
    '/bg-red-500\/20\s+text-red-400/' => 'bg-red-100 text-red-700',
    '/bg-blue-500\/20\s+text-blue-400/' => 'bg-blue-100 text-blue-700',
    '/bg-red-600\/20/' => 'bg-red-50',
    '/bg-blue-600\/30/' => 'bg-blue-100',
    
    // Admin chart fix
    '/rgba\(124,\s*58,\s*237,\s*0\.6\)/' => '#16a34a',
    '/rgba\(124,\s*58,\s*237,\s*1\)/' => '#16a34a',

    // Fix Blade syntax Bug 2
    '/@\{\{\s*\$post->user->username\s*\?\?\s*\'\'\s*\}\}/' => '{{ $post->user->username ?? \'\' }}',
];

$di = new RecursiveDirectoryIterator($viewsDir, RecursiveDirectoryIterator::SKIP_DOTS);
$it = new RecursiveIteratorIterator($di);

foreach ($it as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
        $content = file_get_contents($file);
        $newContent = preg_replace(array_keys($replacements), array_values($replacements), $content);
        
        // Fix duplicate shadow-sm if run repeatedly or matching overlapping patterns
        $newContent = str_replace('shadow-sm border border-gray-100 shadow-sm border border-gray-100', 'shadow-sm border border-gray-100', $newContent);
        
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
