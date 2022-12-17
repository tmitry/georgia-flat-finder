<?php

declare(strict_types=1);

namespace App\Provider;

use App\Flat;
use App\Repository\ImageRepository;
use Exception;

class ProviderParserSS implements ProviderParserInterface
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
                '/<div class="latest_article_each " data-id="([0-9]+)".*<div class="DesktopArticleLayout">.*<img class="owl-lazy" data-src="([^"]+)".*<div class="latest_flat_km">.*([0-9]+?).*<\/div>.*<div class="latest_stair_count">.*([0-9]+?).*<\/div>.*<div class="price-spot dalla" style="">.*<div class="latest_price">.*([0-9]+?)/Uus',
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
                    (float) $matches[5][$i],
                    (float) $matches[3][$i],
                    (int) $matches[4][$i],
                    2,
                    sprintf('https://ss.ge/ru/недвижимость/-%d', (int) $matches[1][$i])
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