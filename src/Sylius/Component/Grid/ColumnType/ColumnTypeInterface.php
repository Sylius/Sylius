<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\ColumnType;

use Sylius\Component\Grid\Definition\Grid;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ColumnTypeInterface
{
    /**
     * Renders the value.
     *
     * @param array|object $data    The data
     * @param string       $name    Name of the column
     * @param array        $options Array of options
     */
    public function render($data, $name, array $options = array());

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setOptions(OptionsResolverInterface $resolver);

    /**
     * @return string
     */
    public function getType();
}
