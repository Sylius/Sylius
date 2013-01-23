<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Sylius kernel.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            // Sylius bundles.
            new Sylius\Bundle\CoreBundle\SyliusCoreBundle(),
            new Sylius\Bundle\WebBundle\SyliusWebBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),

            new Sylius\Bundle\TaxationBundle\SyliusTaxationBundle(),
            new Sylius\Bundle\ShippingBundle\SyliusShippingBundle(),
            new Sylius\Bundle\AddressingBundle\SyliusAddressingBundle(),

            // Core bundles.
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),

            // Third party bundles.
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new FOS\RestBundle\FOSRestBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'testing'))) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/container/'.$this->getEnvironment().'.yml');

        if (is_file($file = __DIR__.'/config/container/'.$this->getEnvironment().'.local.yml')) {
            $loader->load($file);
        }
    }
}
