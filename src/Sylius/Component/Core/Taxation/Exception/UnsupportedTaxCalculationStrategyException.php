<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Taxation\Exception;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class UnsupportedTaxCalculationStrategyException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('Unsupported tax calculation strategy!');
    }
}
