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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DateTimeColumnType extends StringColumnType
{
    /**
     * {@inheritdoc}
     */
    public function render($data, $name, array $options = array())
    {
        $value = parent::render($data, $name, $options);

        if (!$value instanceof \DateTime) {
            throw new \InvalidArgumentException(sprintf('Expected instance of "DateTime", got "%s".', gettype($value)));
        }

        return $value->format('d/m/Y H:i:s');
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'datetime';
    }
}
