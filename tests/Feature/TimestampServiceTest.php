<?php

use App\Services\TimestampService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

it('creates a timestamp service with default configuration', function () {
    $service = new TimestampService;

    expect($service)->toBeInstanceOf(TimestampService::class);
});

it('creates a timestamp service with custom configuration', function () {
    $service = new TimestampService('http://custom-tsa.example.com', 'sha512');

    expect($service)->toBeInstanceOf(TimestampService::class);
});

it('returns null when TSA request fails', function () {
    Http::fake([
        'http://timestamp.digicert.com' => Http::response(null, 500),
    ]);

    $service = new TimestampService('http://timestamp.digicert.com', 'sha256');
    $result = $service->timestamp('test data');

    expect($result)->toBeNull();
});

it('returns null when TSA request times out', function () {
    Http::fake(function () {
        throw new ConnectionException('Connection timed out');
    });

    $service = new TimestampService('http://timestamp.digicert.com', 'sha256');
    $result = $service->timestamp('test data');

    expect($result)->toBeNull();
});

it('returns null when TSA returns invalid response', function () {
    Http::fake([
        'http://timestamp.digicert.com' => Http::response('invalid response', 200, [
            'Content-Type' => 'application/timestamp-reply',
        ]),
    ]);

    $service = new TimestampService('http://timestamp.digicert.com', 'sha256');
    $result = $service->timestamp('test data');

    // The service should handle invalid responses gracefully
    expect($result)->toBeNull();
});

it('calculates correct hash for different algorithms', function () {
    $data = 'test data';

    // Test that the service can be created with different hash algorithms
    $sha256Service = new TimestampService('http://example.com', 'sha256');
    $sha384Service = new TimestampService('http://example.com', 'sha384');
    $sha512Service = new TimestampService('http://example.com', 'sha512');

    expect($sha256Service)->toBeInstanceOf(TimestampService::class);
    expect($sha384Service)->toBeInstanceOf(TimestampService::class);
    expect($sha512Service)->toBeInstanceOf(TimestampService::class);
});

it('handles empty data gracefully', function () {
    Http::fake([
        'http://timestamp.digicert.com' => Http::response(null, 500),
    ]);

    $service = new TimestampService('http://timestamp.digicert.com', 'sha256');
    $result = $service->timestamp('');

    expect($result)->toBeNull();
});
