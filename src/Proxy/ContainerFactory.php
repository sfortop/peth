<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <serhii.borodai@globalgames.net>
 */

namespace Proxy;


use Psr\Container\ContainerInterface;

class ContainerFactory
{
    public function __invoke(ContainerInterface $container) {
        return new Container($container);
    }
}