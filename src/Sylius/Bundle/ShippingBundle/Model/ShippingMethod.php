<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Model;

/**
 * Shipping method model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ShippingMethod implements ShippingMethodInterface
{
    /**
     * Shipping method identifier.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Category.
     *
     * @var ShippingCategoryInterface
     */
    protected $category;

    /**
     * The one of 3 requirement variants.
     *
     * @var integer
     */
    protected $requirement;

    /**
     * Is method enabled?
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * Name.
     *
     * @var string
     */
    protected $name;

    /**
     * Calculator name.
     *
     * @var string
     */
    protected $calculator;

    /**
     * All extra configuration.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Creation date.
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->requirement = ShippingMethodInterface::REQUIREMENT_MATCH_ANY;
        $this->createdAt = new \DateTime('now');
        $this->configuration = array();
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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategory(ShippingCategoryInterface $category = null)
    {
        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequirement()
    {
        return $this->requirement;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequirement($requirement)
    {
        $this->requirement = $requirement;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequirementLabel()
    {
        $labels = self::getRequirementLabels();

        return $labels[$this->requirement];
    }

    /**
     * {@inheritdoc}
     */
    public function matches(ShipmentInterface $shipment)
    {
        if (!$this->enabled) {
            throw new \LogicException('Disabled shipping methods cannot match a shipment');
        }

        if (null === $this->category) {
            return true;
        }

        if (ShippingMethodInterface::REQUIREMENT_MATCH_NONE === $this->requirement) {
            $shippables = $shipment->getShippables();

            foreach ($shippables as $shippable) {
                if ($this->category === $shippable->getCategory()) {
                    return false;
                }
            }
        }

        if (ShippingMethodInterface::REQUIREMENT_MATCH_ANY === $this->requirement) {
            $shippables = $shipment->getShippables();

            foreach ($shippables as $shippable) {
                if ($this->category === $shippable->getCategory()) {
                    return true;
                }
            }

            return false;
        }

        if (ShippingMethodInterface::REQUIREMENT_MATCH_ALL === $this->requirement) {
            $shippables = $shipment->getShippables();

            foreach ($shippables as $shippable) {
                if ($this->category !== $shippable->getCategory()) {
                    return false;
                }
            }
        }

        return true;
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
        $this->enabled = (Boolean) $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculator()
    {
        return $this->calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function setCalculator($calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get the default requirement labels.
     *
     * @return array
     */
    public static function getRequirementLabels()
    {
        return array(
            ShippingMethodInterface::REQUIREMENT_MATCH_NONE => 'None of items have to match method category',
            ShippingMethodInterface::REQUIREMENT_MATCH_ANY  => 'At least 1 item have to match method category',
            ShippingMethodInterface::REQUIREMENT_MATCH_ALL  => 'All items have to match method category',
        );
    }
}
