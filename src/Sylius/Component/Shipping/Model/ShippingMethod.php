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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Translation\Model\AbstractTranslatable;

/**
 * Shipping method model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ShippingMethod extends AbstractTranslatable implements ShippingMethodInterface
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
    protected $categoryRequirement = ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY;

    /**
     * Is method enabled?
     *
     * @var Boolean
     */
    protected $enabled = true;

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
    protected $configuration = array();

    /**
     * Shipping method rules.
     *
     * @var Collection|RuleInterface[]
     */
    protected $rules;

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
        parent::__construct();
        $this->rules = new ArrayCollection();
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule(RuleInterface $rule)
    {
        return $this->rules->contains($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function addRule(RuleInterface $rule)
    {
        if (!$this->hasRule($rule)) {
            $rule->setMethod($this);
            $this->rules->add($rule);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRule(RuleInterface $rule)
    {
        $rule->setMethod(null);
        $this->rules->removeElement($rule);

        return $this;
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

        return $this;
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

        return $this;
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

    /**
     * {@inheritdoc}
     */
    protected function getTranslationEntityClass()
    {
        return get_class().'Translation';
    }
}
