<?php

return [
    'base_url' => getenv('COLLECTION_BASE_URL') ? getenv('COLLECTION_BASE_URL') : "http://mpb.local.com:8055" ,
    'driver' => getenv('COLLECTION_DRIVER') ? getenv('COLLECTION_DRIVER') : "directus" ,
    'token' => getenv('COLLECTION_TOKEN') ? getenv('COLLECTION_TOKEN') : "Bearer Jvh_TuBgaSun1y9D5GomA1o5xJbb0OgM",
    'token_key' => getenv('COLLECTION_TOKEN_KEY') ? getenv('COLLECTION_TOKEN_KEY') : "Authorization",
    'verify_client' => getenv('COLLECTION_VERIFY_CLIENT') === "true" ? true : false,
];
