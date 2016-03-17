<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    new Sylius\Bundle\ThemeBundle\Tests\Functional\Bundle\TestBundle\TestBundle(),

    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),
    new \winzou\Bundle\StateMachineBundle\winzouStateMachineBundle(),

    new Sylius\Bundle\ThemeBundle\SyliusThemeBundle(),
];
