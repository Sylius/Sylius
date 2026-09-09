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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new BabDev\PagerfantaBundle\BabDevPagerfantaBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sylius\Bundle\AttributeBundle\SyliusAttributeBundle(),
            new Sylius\Bundle\LocaleBundle\SyliusLocaleBundle(),
            new Sylius\Bundle\ProductBundle\SyliusProductBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yml');

        if (\PHP_VERSION_ID < 80400) {
            // doctrine/doctrine-bundle 2.x enables native lazy objects by default, but they require
            // PHP 8.4+. On PHP < 8.4 disable them so the container can compile.
            $loader->load(static function (ContainerBuilder $container): void {
                $container->loadFromExtension('doctrine', ['orm' => ['enable_native_lazy_objects' => false]]);
            });
        }
    }
}
