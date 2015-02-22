<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Renderer;

use Sylius\Component\Grid\Definition\Column;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ColumnRenderer implements ColumnRendererInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $columnTypeRegistry;

    /**
     * @var array
     */
    private $resolvedOptions = array();

    /**
     * @param ServiceRegistryInterface $filterRegistry
     */
    public function __construct(ServiceRegistryInterface $columnTypeRegistry)
    {
        $this->columnTypeRegistry = $columnTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, Column $column)
    {
        return $this->columnTypeRegistry->get($column->getType())->render($data, $column->getName(), $this->resolveOptions($column));
    }

    /**
     * @param Column $column
     *
     * @return array
     */
    private function resolveOptions(Column $column)
    {
        $type = $column->getType();
        $name = $column->getName();

        $alias = sprintf('%s_%s', $type, $name);

        if (isset($this->resolvedOptions[$alias])) {
            return $this->resolvedOptions[$alias];
        }

        $optionsResolver = new OptionsResolver();

        $this->columnTypeRegistry->get($type)->setOptions($optionsResolver);

        return $this->resolvedOptions[$alias] = $optionsResolver->resolve($column->getOptions());
    }
}
