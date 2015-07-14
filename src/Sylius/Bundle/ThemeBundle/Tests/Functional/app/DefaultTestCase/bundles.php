<?php

return [
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),

    new Sylius\Bundle\ThemeBundle\Tests\Functional\Bundle\TestBundle\TestBundle(),

    new Sylius\Bundle\ThemeBundle\SyliusThemeBundle(),
];
