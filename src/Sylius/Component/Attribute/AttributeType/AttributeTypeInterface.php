<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\AttributeType;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface AttributeTypeInterface
{
    const DEFAULT_ATTRIBUTE_TYPE = 'text';

    /**
     * @return string
     */
    public function getStorageType();

    /**
     * @return string
     */
    public function getType();
}
