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

final class ArrayGridProvider implements GridProviderInterface
{
    /** @var ArrayToDefinitionConverterInterface */
    private $converter;

    /** @var array[] */
    private $gridConfigurations;

    public function __construct(ArrayToDefinitionConverterInterface $converter, array $gridConfigurations)
    {
        $this->converter = $converter;
        $this->gridConfigurations = $gridConfigurations;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $code): Grid
    {
        if (!array_key_exists($code, $this->gridConfigurations)) {
            throw new UndefinedGridException($code);
        }

        $gridConfiguration = $this->gridConfigurations[$code];

        if (isset($gridConfiguration['extends'], $this->gridConfigurations[$gridConfiguration['extends']])) {
            $gridConfiguration = $this->extend($gridConfiguration, $this->gridConfigurations[$gridConfiguration['extends']]);
        }

        return $this->converter->convert($code, $gridConfiguration);
    }

    private function extend(array $gridConfiguration, array $parentGridConfiguration): array
    {
        unset($parentGridConfiguration['sorting']); // Do not inherit sorting.

        $configuration = array_replace_recursive($parentGridConfiguration, $gridConfiguration);

        unset($configuration['extends']);

        return $configuration;
    }
}
