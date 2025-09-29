<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use BabDev\PagerfantaBundle\BabDevPagerfantaBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\TaxonomyBundle\SyliusTaxonomyBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new BabDevPagerfantaBundle(),
            new DoctrineBundle(),
            new SyliusTaxonomyBundle(),
            new SyliusResourceBundle(),
            new TwigBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/SyliusTaxonomyBundle/cache/' . $this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/SyliusTaxonomyBundle/logs';
    }
}
