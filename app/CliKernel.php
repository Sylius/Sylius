<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/AppKernel.php';

/**
 * Sylius CLI application kernel.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CliKernel extends AppKernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Sylius\Bundle\FixturesBundle\SyliusFixturesBundle(),
        );

        return array_merge($bundles, parent::registerBundles());
    }
}
