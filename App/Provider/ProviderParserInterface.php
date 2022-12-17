<?php

declare(strict_types=1);

namespace App\Provider;

use App\Flat;

interface ProviderParserInterface
{
    /**
     * @param Provider $provider
     * @return Flat[]
     */
    public function parse(Provider $provider): array;
}