<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Provider;

use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ArrayGridProvider implements GridProviderInterface
{
    /**
     * @var Grid[]
     */
    private $grids = array();

    /**
     * ArrayGridProvider constructor.
     * @param ArrayToDefinitionConverterInterface $converter
     * @param array $gridConfigurations
     */
    public function __construct(ArrayToDefinitionConverterInterface $converter, array $gridConfigurations)
    {
        foreach ($gridConfigurations as $code => $gridConfiguration) {
            $this->grids[$code] = $converter->convert($code, $gridConfiguration);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($code)
    {
        if (!array_key_exists($code, $this->grids)) {
            throw new UndefinedGridException($code);
        }

        return $this->grids[$code];
    }
}
