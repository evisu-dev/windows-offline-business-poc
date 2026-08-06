<?php

if (!function_exists('format_bytes')) {
    function format_bytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, 1024));

        return round($bytes / (1024 ** $i), 1) . ' ' . $units[$i];
    }
}
