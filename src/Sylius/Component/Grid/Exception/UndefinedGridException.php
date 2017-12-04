<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Exception;

class UndefinedGridException extends \InvalidArgumentException
{
    /**
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(sprintf('Grid "%s" does not exist.', $code));
    }
}
