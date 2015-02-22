<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Filter;

use Sylius\Component\Grid\DataSource\DataSourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface FilterInterface
{
    /**
     * @param DataSourceInterface $dataSource Data source to manipulate
     * @param string              $name       The name of the filter
     * @param mixed               $data       The submitted data
     * @param array               $options    Array of options
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options);

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setOptions(OptionsResolverInterface $resolver);

    /**
     * @return string
     */
    public function getType();
}
