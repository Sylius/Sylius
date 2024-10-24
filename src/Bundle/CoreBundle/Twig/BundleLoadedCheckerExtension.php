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

namespace Sylius\Bundle\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BundleLoadedCheckerExtension extends AbstractExtension
{
    public function __construct(private array $listOfBundles)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_bundle_loaded_checker', [$this, 'isBundleLoaded']),
        ];
    }

    public function isBundleLoaded(string $bundleName): bool
    {
        return array_key_exists($bundleName, $this->listOfBundles);
    }
}
