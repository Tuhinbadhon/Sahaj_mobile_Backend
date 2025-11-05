<?php

return [
    // Remote source for customer data (static JSON or API)
    // Defaults to the provided OUTPUT.json gist. Override via env.
    'source_url' => env('CUSTOMERS_SOURCE_URL', 'https://gist.githubusercontent.com/jasonkliu/e6a3c77029e891f9c630ff83ff62e5ff/raw/f8269ba36806023abc9baad148628d4346744b89/OUTPUT.json'),

    // Cache TTL for remote data in seconds (0 to disable)
    'cache_ttl' => env('CUSTOMERS_CACHE_TTL', 300),
];
