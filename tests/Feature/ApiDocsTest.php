<?php

declare(strict_types=1);

test('the OpenAPI document is generated and lists the task endpoints', function () {
    $response = $this->getJson('/docs/api.json');

    $response->assertOk();
    expect($response->json('paths'))->toHaveKeys(['/tasks', '/tags', '/activity', '/auth/token']);
    expect($response->json('components.securitySchemes.http.scheme'))->toBe('bearer');
});

test('the documentation UI page is reachable', function () {
    $this->get('/docs/api')->assertOk();
});
