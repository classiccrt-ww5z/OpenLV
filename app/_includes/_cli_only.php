<?php
// Guards maintenance/worker scripts so they can only ever be executed from the command
// line. Without this, anything under /_crons/ or admin/_scripts/ is reachable over HTTP
// and will happily rewrite rows for every user.
if (PHP_SAPI !== 'cli') {
    if (!headers_sent()) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
    }
    exit('This script can only be run from the command line.');
}

// Shared secret for the transcode worker. Must be provided via the CONVERTER_KEY
// environment variable; there is deliberately no default value.
function converter_key() {
    return (string)getenv('CONVERTER_KEY');
}

function converter_key_valid($supplied) {
    $key = converter_key();
    if ($key === '' || !is_string($supplied) || $supplied === '') {
        return false;
    }
    return hash_equals($key, $supplied);
}