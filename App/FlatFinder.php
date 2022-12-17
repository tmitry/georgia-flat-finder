<?php

declare(strict_types=1);

namespace App;

use App\Provider\ProviderParserFactory;
use App\Repository\ImageRepository;
use App\Repository\ProviderRepository;
use Exception;

class FlatFinder
{
    private const FIND_DELAY = 300;

    private ProviderRepository $providerRepository;

    private MessageSender $messageSender;

    private ProviderParserFactory $providerParserFactory;

    private ImageRepository $imageRepository;

    public function __construct(
        ProviderRepository $providerRepository,
        MessageSender $messageSender,
        ProviderParserFactory $providerParserFactory,
        ImageRepository $imageRepository
    ) {
        $this->providerRepository = $providerRepository;
        $this->messageSender = $messageSender;
        $this->providerParserFactory = $providerParserFactory;
        $this->imageRepository = $imageRepository;
    }

    public function find(): void
    {
        while (true) {
            $providers = $this->providerRepository->findAll();

            $flatsCount = 0;

            $errors = [];

            foreach ($providers as $provider) {
                if ($provider->isHidden()) {
                    continue;
                }

                $parser = $this->providerParserFactory->create($provider);

                try {
                    $flats = $parser->parse($provider);
                } catch (Exception $exception) {
                    $errors[] = sprintf("%s: %s", $provider->getSource(), $exception->getMessage());
                    continue;
                }

                foreach ($flats as $flat) {
//                    $this->messageSender->send($flat);

                    if ($flat->getId() > $provider->getLastId()) {
                        $provider->setLastId($flat->getId());
                    }

                    $this->imageRepository->add($flat->getPhotoHash(), $provider->getSource());
                }

                $flatsCount += count($flats);

                $this->providerRepository->updateLastId($provider->getId(), $provider->getLastId());
            }

            echo sprintf(
                "%s: %d%s\n",
                date("Y-m-d H:i:s"),
                $flatsCount,
                $errors ? " Errors: " . implode("; ", $errors) : ''
            );

            sleep(self::FIND_DELAY);
        }
    }
}