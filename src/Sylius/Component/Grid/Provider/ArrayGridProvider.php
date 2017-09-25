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

namespace Sylius\Component\Grid\Provider;

use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ArrayGridProvider implements GridProviderInterface
{
    /**
     * @var Grid[]
     */
    private $grids = [];

    /**
     * @param ArrayToDefinitionConverterInterface $converter
     * @param array $gridConfigurations
     */
    public function __construct(ArrayToDefinitionConverterInterface $converter, array $gridConfigurations)
    {
        foreach ($gridConfigurations as $code => $gridConfiguration) {
            if (isset($gridConfiguration['extends'], $gridConfigurations[$gridConfiguration['extends']])) {
                $gridConfiguration = $this->extend($gridConfiguration, $gridConfigurations[$gridConfiguration['extends']]);
            }

            $this->grids[$code] = $converter->convert($code, $gridConfiguration);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $code): Grid
    {
        if (!array_key_exists($code, $this->grids)) {
            throw new UndefinedGridException($code);
        }

        // Need to clone grid definition in case of displaying on one page two grids using the same grid definition
        return clone $this->grids[$code];
    }

    /**
     * @param array $gridConfiguration
     * @param array $parentGridConfiguration
     *
     * @return array
     */
    private function extend(array $gridConfiguration, array $parentGridConfiguration): array
    {
        unset($parentGridConfiguration['sorting']); // Do not inherit sorting.

        $configuration = array_replace_recursive($parentGridConfiguration, $gridConfiguration);

        unset($configuration['extends']);

        return $configuration;
    }
}
