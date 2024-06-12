<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Http\Middleware;

use ChaseConey\LaravelDatadogHelper\Middleware\LaravelDatadogMiddleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DatadogMetrics extends LaravelDatadogMiddleware
{
    /**
     * Logs the duration of a specific request through the application
     *
     * @param Request $request
     * @param Response $response
     * @param float $startTime
     */
    protected static function logDuration(Request $request, Response $response, $startTime)
    {
        $duration = microtime(true) - $startTime;
        $tags = [
            'action' => 'error_page',
            'api' => is_api_request() ? 'true' : 'false',
            'controller' => 'error',
            'namespace' => 'error',
            'status_code' => $response->getStatusCode(),
            ...app('route-section')->getOriginal(),
        ];
        unset($tags['section']);

        \Datadog::timing(
            $GLOBALS['cfg']['datadog-helper']['prefix_web'].'.request_time',
            $duration,
            $GLOBALS['cfg']['datadog-helper']['request_sample_rate'],
            $tags,
        );
    }
}
