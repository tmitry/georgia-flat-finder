<?php

declare(strict_types=1);

use App\DataStorage;
use App\FlatFinder;
use App\MessageSender;
use App\Provider\ProviderParserFactory;
use App\Repository\ImageRepository;
use App\Repository\ProviderRepository;

spl_autoload_register(function($class) {
    include str_replace('\\', '/', $class) . '.php';
});

date_default_timezone_set('Asia/Tbilisi');

$pdo = new PDO("sqlite:db");

$imageRepository = new ImageRepository($pdo);
$providerRepository = new ProviderRepository($pdo);

$ff = new FlatFinder(
    $providerRepository,
    new MessageSender(),
    new ProviderParserFactory($imageRepository),
    $imageRepository
);

$ff->find();