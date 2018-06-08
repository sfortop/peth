<?php
/**
 * Copyright Serhii Borodai (c) 2018.
 */

use Psr\Container\ContainerInterface;


/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

require __DIR__ . '/../vendor/autoload.php';

(function () {
    /** @var ContainerInterface $container */
    $container = require __DIR__ . '/../config/container.php';
    $transactionReader = $container->get(TransactionChecker::class);

    $transactionReader->process();
})();