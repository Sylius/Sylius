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

    new FOS\RestBundle\FOSRestBundle(),
    new JMS\SerializerBundle\JMSSerializerBundle(),
    new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),

    new Sylius\Bundle\ThemeBundle\SyliusThemeBundle(),

    new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
];
