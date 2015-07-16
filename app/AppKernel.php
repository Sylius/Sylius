<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sylius\Bundle\CoreBundle\Kernel\Kernel;

/**
 * Sylius application kernel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles[] = new Sylius\BackendBundle\SyliusBackendBundle();

        if (in_array($this->environment, array('dev', 'test'))) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return array_merge(parent::registerBundles(), $bundles);
    }
}
