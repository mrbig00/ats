<?php

return [

    'disk_name' => env('MEDIA_DISK', 'local'),

    'max_file_size' => 1024 * 1024 * 10,

    'queue_connection_name' => env('QUEUE_CONNECTION', 'sync'),

    'queue_name' => '',

    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),

    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    'enable_temporary_uploads_session_affinity' => true,

    'generate_thumbnails_for_temporary_uploads' => true,

    'file_namer' => Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,

    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    'file_remover_class' => Spatie\MediaLibrary\Support\FileRemover\DefaultFileRemover::class,

    'url_generator' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    'moves_media_on_update' => false,

    'version_urls' => false,

    'image_optimizers' => [],

    'image_generators' => [],

    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),

    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    'jobs' => [
        'perform_conversions' => Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    'responsive_images' => [
        'width_calculator' => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,
        'use_tiny_placeholders' => true,
        'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    'remote' => [
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],

    'media_downloader' => Spatie\MediaLibrary\Downloaders\DefaultDownloader::class,

    'temporary_directory_path' => null,

    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    'ffmpeg_timeout' => 3600,

    'ffmpeg_threads' => 0,

    'force_lazy_loading' => env('FORCE_MEDIA_LIBRARY_LAZY_LOADING', true),

];
