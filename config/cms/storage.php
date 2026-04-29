<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media / upload disk
    |--------------------------------------------------------------------------
    |
    | Disk name from config/filesystems.php used for CMS uploads (images, etc.).
    | Use "public" for local storage linked to public/storage, or "s3" for
    | AWS S3. Set AWS_URL to your CloudFront (or CDN) origin URL for public URLs.
    |
    */
    'disk' => env('DISK', 'public'),

];
