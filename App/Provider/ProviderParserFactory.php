<?php

declare(strict_types=1);

namespace App\Provider;

use App\Repository\ImageRepository;
use Exception;

class ProviderParserFactory
{
    private ImageRepository $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function create(Provider $provider): ProviderParserInterface
    {
        if ($provider->isSourceMyHome()) {
            return new ProviderParserMyHome($this->imageRepository);
        }

        if ($provider->isSourceSS()) {
            return new ProviderParserSS($this->imageRepository);
        }

        if ($provider->isSourceM2()) {
            return new ProviderParserM2($this->imageRepository);
        }

        throw new Exception('Invalid source.');
    }
}