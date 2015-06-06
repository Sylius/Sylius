<?php

use Sylius\Bundle\ThemeBundle\SyliusThemeBundle;
use Sylius\Bundle\ThemeBundle\Tests\Functional\Bundle\TestBundle\TestBundle;

return array(
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),
    new Symfony\Bundle\AsseticBundle\AsseticBundle(),

    new TestBundle(),

    new SyliusThemeBundle(),
);
