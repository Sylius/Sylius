<?php

namespace Sylius\Bundle\FixturesBundle\Loader;

interface LoaderInterface
{
    public static function loadSet($type, $suite = 'default');
} 