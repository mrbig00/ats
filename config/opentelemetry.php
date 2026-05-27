<?php

use Keepsuit\LaravelOpenTelemetry\Instrumentation;
use Keepsuit\LaravelOpenTelemetry\Support\ResourceAttributesParser;
use Keepsuit\LaravelOpenTelemetry\TailSampling;
use Keepsuit\LaravelOpenTelemetry\WorkerMode;
use OpenTelemetry\SDK\Common\Configuration\Variables;

$resourceAttributes = ResourceAttributesParser::parse((string) env(Variables::OTEL_RESOURCE_ATTRIBUTES, ''));

if ($version = env('APP_VERSION')) {
    $resourceAttributes['service.version'] = $version;
}

return [
    'disabled' => filter_var(env(Variables::OTEL_SDK_DISABLED, false), FILTER_VALIDATE_BOOLEAN),

    'service_name' => env(Variables::OTEL_SERVICE_NAME, 'ats'),

    'service_instance_id' => env('OTEL_SERVICE_INSTANCE_ID'),

    'resource_attributes' => $resourceAttributes,

    'user_context' => filter_var(env('OTEL_USER_CONTEXT', true), FILTER_VALIDATE_BOOLEAN),

    'propagators' => env(Variables::OTEL_PROPAGATORS, 'tracecontext'),

    'metrics' => [
        'exporter' => env(Variables::OTEL_METRICS_EXPORTER, 'otlp'),
    ],

    'traces' => [
        'exporter' => env(Variables::OTEL_TRACES_EXPORTER, 'otlp'),

        'sampler' => [
            'parent' => filter_var(env('OTEL_TRACES_SAMPLER_PARENT', true), FILTER_VALIDATE_BOOLEAN),
            'type' => env('OTEL_TRACES_SAMPLER_TYPE', 'always_on'),
            'args' => [
                'ratio' => env('OTEL_TRACES_SAMPLER_TRACEIDRATIO_RATIO', 0.05),
            ],
            'tail_sampling' => [
                'enabled' => filter_var(env('OTEL_TRACES_TAIL_SAMPLING_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
                'decision_wait' => (int) env('OTEL_TRACES_TAIL_SAMPLING_DECISION_WAIT', 5000),
                'rules' => [
                    TailSampling\Rules\ErrorsRule::class => filter_var(env('OTEL_TRACES_TAIL_SAMPLING_RULE_KEEP_ERRORS', true), FILTER_VALIDATE_BOOLEAN),
                    TailSampling\Rules\SlowTraceRule::class => [
                        'enabled' => filter_var(env('OTEL_TRACES_TAIL_SAMPLING_RULE_SLOW_TRACES', true), FILTER_VALIDATE_BOOLEAN),
                        'threshold_ms' => (int) env('OTEL_TRACES_TAIL_SAMPLING_SLOW_TRACES_THRESHOLD_MS', 2000),
                    ],
                ],
            ],
        ],

        'processors' => [],
    ],

    'logs' => [
        'exporter' => env(Variables::OTEL_LOGS_EXPORTER, 'otlp'),
        'inject_trace_id' => true,
        'trace_id_field' => 'trace_id',
        'processors' => [],
    ],

    'exporters' => [
        'otlp' => [
            'driver' => 'otlp',
            'endpoint' => env(Variables::OTEL_EXPORTER_OTLP_ENDPOINT, 'http://localhost:4318'),
            'protocol' => env(Variables::OTEL_EXPORTER_OTLP_PROTOCOL, 'http/protobuf'),
            'max_retries' => (int) env('OTEL_EXPORTER_OTLP_MAX_RETRIES', 3),
            'traces_timeout' => (int) env(Variables::OTEL_EXPORTER_OTLP_TRACES_TIMEOUT, env(Variables::OTEL_EXPORTER_OTLP_TIMEOUT, 10000)),
            'traces_headers' => (string) env(Variables::OTEL_EXPORTER_OTLP_TRACES_HEADERS, env(Variables::OTEL_EXPORTER_OTLP_HEADERS, '')),
            'traces_protocol' => env(Variables::OTEL_EXPORTER_OTLP_TRACES_PROTOCOL),
            'metrics_timeout' => (int) env(Variables::OTEL_EXPORTER_OTLP_METRICS_TIMEOUT, env(Variables::OTEL_EXPORTER_OTLP_TIMEOUT, 10000)),
            'metrics_headers' => (string) env(Variables::OTEL_EXPORTER_OTLP_METRICS_HEADERS, env(Variables::OTEL_EXPORTER_OTLP_HEADERS, '')),
            'metrics_protocol' => env(Variables::OTEL_EXPORTER_OTLP_METRICS_PROTOCOL),
            'metrics_temporality' => env(Variables::OTEL_EXPORTER_OTLP_METRICS_TEMPORALITY_PREFERENCE),
            'logs_timeout' => (int) env(Variables::OTEL_EXPORTER_OTLP_LOGS_TIMEOUT, env(Variables::OTEL_EXPORTER_OTLP_TIMEOUT, 10000)),
            'logs_headers' => (string) env(Variables::OTEL_EXPORTER_OTLP_LOGS_HEADERS, env(Variables::OTEL_EXPORTER_OTLP_HEADERS, '')),
            'logs_protocol' => env(Variables::OTEL_EXPORTER_OTLP_LOGS_PROTOCOL),
        ],

        'zipkin' => [
            'driver' => 'zipkin',
            'endpoint' => env(Variables::OTEL_EXPORTER_ZIPKIN_ENDPOINT, 'http://localhost:9411'),
            'timeout' => env(Variables::OTEL_EXPORTER_ZIPKIN_TIMEOUT, 10000),
            'max_retries' => (int) env('OTEL_EXPORTER_ZIPKIN_MAX_RETRIES', 3),
        ],
    ],

    'instrumentation' => [
        Instrumentation\HttpServerInstrumentation::class => [
            'enabled' => filter_var(env('OTEL_INSTRUMENTATION_HTTP_SERVER', true), FILTER_VALIDATE_BOOLEAN),
            'excluded_paths' => ['/up'],
            'excluded_methods' => [],
            'allowed_headers' => [],
            'sensitive_headers' => [],
            'sensitive_query_parameters' => [],
        ],

        Instrumentation\HttpClientInstrumentation::class => [
            'enabled' => filter_var(env('OTEL_INSTRUMENTATION_HTTP_CLIENT', true), FILTER_VALIDATE_BOOLEAN),
            'manual' => false,
            'allowed_headers' => [],
            'sensitive_headers' => [],
            'sensitive_query_parameters' => [],
        ],

        Instrumentation\QueryInstrumentation::class => filter_var(env('OTEL_INSTRUMENTATION_QUERY', true), FILTER_VALIDATE_BOOLEAN),

        Instrumentation\RedisInstrumentation::class => filter_var(env('OTEL_INSTRUMENTATION_REDIS', true), FILTER_VALIDATE_BOOLEAN),

        Instrumentation\QueueInstrumentation::class => filter_var(env('OTEL_INSTRUMENTATION_QUEUE', true), FILTER_VALIDATE_BOOLEAN),

        Instrumentation\CacheInstrumentation::class => filter_var(env('OTEL_INSTRUMENTATION_CACHE', true), FILTER_VALIDATE_BOOLEAN),

        Instrumentation\EventInstrumentation::class => [
            'enabled' => filter_var(env('OTEL_INSTRUMENTATION_EVENT', true), FILTER_VALIDATE_BOOLEAN),
            'excluded' => [],
        ],

        Instrumentation\ViewInstrumentation::class => filter_var(env('OTEL_INSTRUMENTATION_VIEW', true), FILTER_VALIDATE_BOOLEAN),

        Instrumentation\LivewireInstrumentation::class => filter_var(env('OTEL_INSTRUMENTATION_LIVEWIRE', true), FILTER_VALIDATE_BOOLEAN),

        Instrumentation\ConsoleInstrumentation::class => [
            'enabled' => filter_var(env('OTEL_INSTRUMENTATION_CONSOLE', true), FILTER_VALIDATE_BOOLEAN),
            'commands' => [],
        ],

        Instrumentation\ScoutInstrumentation::class => filter_var(env('OTEL_INSTRUMENTATION_SCOUT', true), FILTER_VALIDATE_BOOLEAN),
    ],

    'worker_mode' => [
        'flush_after_each_iteration' => filter_var(env('OTEL_WORKER_MODE_FLUSH_AFTER_EACH_ITERATION', false), FILTER_VALIDATE_BOOLEAN),
        'metrics_collect_interval' => (int) env('OTEL_WORKER_MODE_COLLECT_INTERVAL', 60),
        'detectors' => [
            WorkerMode\Detectors\OctaneWorkerModeDetector::class,
            WorkerMode\Detectors\QueueWorkerModeDetector::class,
        ],
    ],
];
