<?php

declare(strict_types=1);

namespace App;

class Flat
{
    private int $id;

    private string $photo;

    private string $photoHash;

    private float $price;

    private float $area;

    private int $stage;

    private int $rooms;

    private string $url;

    public function __construct(
        int $id,
        string $photo,
        string $photoHash,
        float $price,
        float $area,
        int $stage,
        int $rooms,
        string $url
    ) {
        $this->id = $id;
        $this->photo = $photo;
        $this->photoHash = $photoHash;
        $this->price = $price;
        $this->area = $area;
        $this->stage = $stage;
        $this->rooms = $rooms;
        $this->url = $url;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function getPhotoHash(): string
    {
        return $this->photoHash;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getArea(): float
    {
        return $this->area;
    }

    public function getStage(): int
    {
        return $this->stage;
    }

    public function getRooms(): int
    {
        return $this->rooms;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function __toString()
    {
        return <<<TEXT
{$this->getUrl()}
Price: {$this->price} $
Area: {$this->area} m2
Stage: {$this->stage}
Rooms: {$this->rooms}
TEXT;

    }
}