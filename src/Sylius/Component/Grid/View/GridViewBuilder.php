<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\View;

use Sylius\Component\Grid\Data\DataFactoryInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GridViewBuilder implements GridViewBuilderInterface
{
    /**
     * @var DataFactoryInterface
     */
    private $dataFactory;

    /**
     * @param DataFactoryInterface $dataFactory
     */
    public function __construct(DataFactoryInterface $dataFactory)
    {
        $this->dataFactory = $dataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function build(Grid $grid, Parameters $parameters)
    {
        $data = $this->dataFactory->createData($grid, $parameters);

        return new GridView($data, $grid, $parameters);
    }
}
