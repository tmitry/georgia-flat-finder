<?php

declare(strict_types=1);

namespace App\Provider;

use App\Flat;
use App\Repository\ImageRepository;
use Exception;

class ProviderParserM2 implements ProviderParserInterface
{
    private const URL = 'https://m2rent.ge/';

    private ImageRepository $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    /**
     * @param Provider $provider
     * @return Flat[]
     * @throws Exception
     */
    public function parse(Provider $provider): array
    {
        $flats = [];

        $contextOptions = [
            "ssl" => [
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ],
        ];

        for ($p = $provider->getPages(); $p > 0; $p--) {
            $content = file_get_contents($provider->getUrlByPage($p), false, stream_context_create($contextOptions));

            if ($content === false) {
                throw new Exception('Can not open.');
            }

            preg_match_all(
                '/<div class="PropertyItemDetail">.*<a href="property\/([0-9]+)\/">.*<div class="Image" style="background-image: url\(\'([^\']+)\'\).*<span class="Value">([0-9]+) m²<\/span>.*<div class="Option Rooms">.*<span class="Value">([0-9]+)<\/span>.*<\/div>.*<div class="Option Floor">.*<span class="Value">([0-9]+)<\/span>.*<\/div>.*<div class="Price".*data-currency="([0-9]+)\$/Uus',
                $content,
                $matches
            );

            if (!isset($matches[0][0])) {
                throw new Exception('Can not parse content.');
            }

            for ($i = 0; $i < count($matches[0]); $i++) {
                $flat = new Flat(
                    (int) $matches[1][$i],
                    self::URL . $matches[2][$i],
                    sha1(file_get_contents(self::URL . $matches[2][$i], false, stream_context_create($contextOptions))),
                    (float) $matches[6][$i],
                    (float) $matches[3][$i],
                    (int) $matches[5][$i],
                    (int) $matches[4][$i],
                    sprintf('%sen/property/%d/', self::URL, (int) $matches[1][$i])
                );

                if (
                    !array_filter($flats, function (Flat $f) use ($flat) {
                        return $f->getId() === $flat->getId();
                    })
                    && $flat->getId() > $provider->getLastId()
                    && !$this->imageRepository->findAllByHash($flat->getPhotoHash())
                ) {
                    $flats[] = $flat;
                }
            }
        }

        return $flats;
    }
}