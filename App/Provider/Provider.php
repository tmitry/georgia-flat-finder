<?php

declare(strict_types=1);

namespace App\Provider;

class Provider
{
    public const SOURCE_SS      = 'ss';
    public const SOURCE_MY_HOME = 'my_home';
    public const SOURCE_M2      = 'm2';

    public const PATTERN_PAGE = '{page}';

    private int $id;

    private string $source;

    private int $lastId;

    private string $url;

    private ?string $comment;

    private bool $isHidden;

    private int $pages;

    public function __construct(int $id, string $source, int $lastId, string $url, ?string $comment, bool $isHidden, int $pages)
    {
        $this->id = $id;
        $this->source = $source;
        $this->lastId = $lastId;
        $this->url = $url;
        $this->comment = $comment;
        $this->isHidden = $isHidden;
        $this->pages = $pages;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getLastId(): int
    {
        return $this->lastId;
    }

    public function setLastId(int $lastId): void
    {
        $this->lastId = $lastId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUrlByPage(int $page): string
    {
        return str_replace(self::PATTERN_PAGE, (string) $page, $this->getUrl());
    }

    public function isSourceSS(): bool
    {
        return $this->source === self::SOURCE_SS;
    }

    public function isSourceMyHome(): bool
    {
        return $this->source === self::SOURCE_MY_HOME;
    }

    public function isSourceM2(): bool
    {
        return $this->source === self::SOURCE_M2;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    public function getPages(): int
    {
        return $this->pages;
    }
}