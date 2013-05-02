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
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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
    protected $categoryRequirement;

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
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->categoryRequirement = ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY;
        $this->createdAt = new \DateTime();
        $this->configuration = array();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name;
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
    public function getCategoryRequirement()
    {
        return $this->categoryRequirement;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryRequirement($categoryRequirement)
    {
        $this->categoryRequirement = $categoryRequirement;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryRequirementLabel()
    {
        $labels = self::getCategoryRequirementLabels();

        return $labels[$this->categoryRequirement];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ShippablesAwareInterface $shippablesAware)
    {
        if (!$this->enabled) {
            throw new \LogicException('Disabled shipping methods cannot match a shipment');
        }

        if (null === $this->category) {
            return true;
        }

        $shippables = $shippablesAware->getShippables();

        if (ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE === $this->categoryRequirement) {
            foreach ($shippables as $shippable) {
                if ($this->category === $shippable->getShippingCategory()) {
                    return false;
                }
            }
        }

        if (ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY === $this->categoryRequirement) {
            foreach ($shippables as $shippable) {
                if ($this->category === $shippable->getShippingCategory()) {
                    return true;
                }
            }

            return false;
        }

        if (ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL === $this->categoryRequirement) {
            foreach ($shippables as $shippable) {
                if ($this->category !== $shippable->getShippingCategory()) {
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

    /**
     * Get the default requirement labels.
     *
     * @return array
     */
    public static function getCategoryRequirementLabels()
    {
        return array(
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE => 'None of items have to match method category',
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY  => 'At least 1 item have to match method category',
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL  => 'All items have to match method category',
        );
    }
}
