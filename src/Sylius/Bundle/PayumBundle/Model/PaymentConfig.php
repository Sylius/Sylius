<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Model;

use Payum\Core\Model\PaymentConfigInterface;

class PaymentConfig implements PaymentConfigInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $paymentName;

    /**
     * @var string
     */
    protected $factoryName;

    /**
     * @var array
     */
    protected $config;

    public function __construct()
    {
        $this->config = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentName()
    {
        return $this->paymentName;
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentName($paymentName)
    {
        $this->paymentName = $paymentName;
    }

    /**
     * {@inheritDoc}
     */
    public function getFactoryName()
    {
        return $this->factoryName;
    }

    /**
     * {@inheritDoc}
     */
    public function setFactoryName($factoryName)
    {
        $this->factoryName = $factoryName;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }
}
