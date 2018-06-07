<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

namespace Proxy;


use Psr\Container\ContainerInterface;

class ContainerFactory
{
    public function __invoke(ContainerInterface $container) {
        return new Container($container);
    }
}