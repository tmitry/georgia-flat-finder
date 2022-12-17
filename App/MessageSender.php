<?php

declare(strict_types=1);

namespace App;

class MessageSender
{
    private const BOT_API_TOKEN = "5250358970:AAFaf1CvhoYaBzDLHneul86_lPJ0j06Cbd0";

    private const CHAT_ID = "-1001609190056";

    public function send(Flat $flat): void
    {
        $data = [
            'chat_id' => self::CHAT_ID,
            'text'    => (string) $flat
        ];

        file_get_contents($this->getUrl() . http_build_query($data));
    }

    private function getUrl(): string
    {
        return sprintf("https://api.telegram.org/bot%s/sendMessage?", self::BOT_API_TOKEN);
    }
}