<?php

namespace App\Trait;

trait FakeProviderCall
{
    private function fakeAuthApiResponse(string $urlPattern, array $responseData): void
    {
        Http::fake([
            $urlPattern => Http::response($responseData, 200),
        ]);
    }
}
