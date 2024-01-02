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

namespace Sylius\Bundle\CoreBundle\Grid\Provider;

use Psr\Container\ContainerInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Webmozart\Assert\Assert;

final class ConfigurableProvider implements GridProviderInterface
{
    public function __construct(
        private ContainerInterface $providers,
        private array $configuration,
    ) {
    }

    public function get(string $code): Grid
    {
        return $this->getProviderFromConfiguration($code)
            ->get($code)
        ;
    }

    private function getProviderFromConfiguration(string $code): GridProviderInterface
    {
        /** @var string|null $type */
        $type = $this->configuration['grids'][$code]['type'] ?? null;

        if (null === $type) {
            return $this->getDefaultProvider();
        }

        return $this->getProvider($type);
    }

    private function getDefaultProvider(): GridProviderInterface
    {
        /** @var string|null $defaultType */
        $defaultType = $this->configuration['default_type'] ?? null;

        if (null === $defaultType) {
            throw new \RuntimeException('No default type for grids was found but it should.');
        }

        return $this->getProvider($defaultType);
    }

    private function getProvider(string $type): GridProviderInterface
    {
        $provider = $this->providers->get($type);

        if (null === $provider) {
            throw new \RuntimeException(sprintf('Provider with type "%s" was not found but it should.', $type));
        }

        Assert::isInstanceOf($provider, GridProviderInterface::class);

        return $provider;
    }
}
