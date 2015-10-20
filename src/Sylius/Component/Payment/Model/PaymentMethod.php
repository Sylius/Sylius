<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Model;

use Sylius\Component\Payment\Calculator\DefaultFeeCalculators;
use Sylius\Component\Translation\Model\AbstractTranslatable;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PaymentMethod extends AbstractTranslatable implements PaymentMethodInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var Boolean
     */
    protected $enabled = true;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $gateway;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $feeCalculator = DefaultFeeCalculators::FIXED;

    /**
     * @var array
     */
    protected $feeCalculatorConfiguration = array();

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->translate()->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (Boolean)$enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->translate()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->translate()->setName($name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->translate()->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->translate()->setDescription($description);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * {@inheritdoc}
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeeCalculator()
    {
        return $this->feeCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeeCalculator($feeCalculator)
    {
        $this->feeCalculator = $feeCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeeCalculatorConfiguration()
    {
        return $this->feeCalculatorConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeeCalculatorConfiguration(array $feeCalculatorConfiguration)
    {
        $this->feeCalculatorConfiguration = $feeCalculatorConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
