#!/usr/local/bin/php
<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

use Daemon\BlockReader;
use Psr\Container\ContainerInterface;

/**
 * Created by Serhii Borodai <serhii.borodai@globalgames.net>
 */

require __DIR__ . '/../vendor/autoload.php';

(function () {
    /** @var ContainerInterface $container */
    $container = require __DIR__ . '/../config/container.php';
    $blockReader = $container->get(BlockReader::class);

    $blockReader->process();
})();