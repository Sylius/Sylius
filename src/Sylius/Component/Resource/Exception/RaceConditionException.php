<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Exception;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class RaceConditionException extends \Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('Operated entity was previously modified.');
    }
}
