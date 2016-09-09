<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker\Eligibility;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class UnsupportedPromotionException extends \InvalidArgumentException
{
    /**
     * @param string|null $message
     * @param \Exception $previous
     */
    public function __construct($message = null, \Exception $previous = null)
    {
        parent::__construct($message ?: 'Unsupported promotion.', 0, $previous);
    }
}
