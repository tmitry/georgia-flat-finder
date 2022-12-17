<?php

declare(strict_types=1);

namespace App\Provider;

use App\Flat;
use App\Repository\ImageRepository;
use Exception;

class ProviderParserMyHome implements ProviderParserInterface
{
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

        for ($p = $provider->getPages(); $p > 0; $p--) {
            $content = file_get_contents($provider->getUrl() . $p);

            if ($content === false) {
                throw new Exception('Can not open.');
            }

            preg_match_all(
                '/<div class="statement-card[^"]?" data-product-id="([0-9]+)" data-thumb="([^"]+)">.+<b class="item-price-usd  mr-2">([0-9]+)<\/b>.*<div class="item-size">([0-9\.]+) <.*<span>Этаж ([0-9]+)<\/span>.*<span>Комн. ([0-9]+)<\/span>.*<span>Спальня ([0-9])+<\/span>/Us',
                $content,
                $matches
            );

            if (!isset($matches[0][0])) {
                throw new Exception('Can not parse content.');
            }

            for ($i = 0; $i < count($matches[0]); $i++) {
                $flat = new Flat(
                    (int) $matches[1][$i],
                    $matches[2][$i],
                    sha1_file($matches[2][$i]),
                    (float) $matches[3][$i],
                    (float) $matches[4][$i],
                    (int) $matches[5][$i],
                    (int) $matches[6][$i],
                    sprintf('https://www.myhome.ge/ru/pr/%d/', (int) $matches[1][$i])
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