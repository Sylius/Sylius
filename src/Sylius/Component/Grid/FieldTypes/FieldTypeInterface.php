<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\FieldTypes;

use Sylius\Component\Grid\Definition\Field;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface FieldTypeInterface
{
    /**
     * @param Field $field
     * @param mixed $data
     *
     * @return mixed
     */
    public function render(Field $field, $data);
}
