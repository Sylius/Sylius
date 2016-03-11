<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Data;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class UnsupportedDriverException extends \InvalidArgumentException
{
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct(sprintf('Grid data driver "%s" is not supported.', $name));
    }
}
