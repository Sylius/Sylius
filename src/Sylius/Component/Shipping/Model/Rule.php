<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

use Sylius\Component\Resource\Model\Rule as BaseRule;

/**
 * Shipping method rule model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Rule extends BaseRule implements RuleInterface
{
    /**
     * @var ShippingMethodInterface
     */
    protected $method;

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod(ShippingMethodInterface $method = null)
    {
        $this->method = $method;

        return $this;
    }
}
