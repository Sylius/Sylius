<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BundleAvailableCheckerExtension extends AbstractExtension
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_bundle_available', [$this, 'isBundleAvailable']),
        ];
    }

    public function isBundleAvailable(string $bundleName): bool
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        foreach ($bundles as $bundle) {
            if (strpos($bundle, $bundleName) > 0) {
                return true;
            }
        }

        return false;
    }
}
