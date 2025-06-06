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

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\Provider\ChainProvider;
use Sylius\Component\Grid\Provider\ArrayGridProvider;
use Sylius\Component\Grid\Exception\UndefinedGridException;

/**
* From 3.0 forward Sylius does not support the chain provider and only use Service grids. This is a BC layer for
* allowing users to migrate on a per grid basis to the new configuration.
*
* @deprecated will be removed in 3.0
*/
final class OverrideGridProvider implements GridProviderInterface
{
    /** @param array<string, mixed> $configuration */
    public function __construct(
        private array $configuration,
        private ChainProvider $chainGridProvider,
        private ArrayGridProvider $arrayGridProvider,
    ) {
    }

    public function get(string $code): Grid
    {
        $useYamlGrid = $this->configuration['grids'][$code]['use_legacy_yaml_config'] ?? true;
        if ($useYamlGrid) {
            try {
                return $this->arrayGridProvider->get($code);
            } catch(UndefinedGridException) {
            }
        }

        return $this->chainGridProvider->get($code);
    }
}
