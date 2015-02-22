<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\DataSource;

/**
 * @author Paweł Jędrzejewski <pawel@svaluelius.org>
 */
interface DataSourceInterface
{
    const CONDITION_AND = 'and';
    const CONDITION_OR  = 'or';

    public function restrict($expression, $condition = self::CONDITION_AND);
    public function getExpressionBuilder();
    public function getData();
}
