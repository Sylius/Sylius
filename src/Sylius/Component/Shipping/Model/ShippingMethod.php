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
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Translation\Model\TranslatableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ShippingMethod implements ShippingMethodInterface
{
    use TimestampableTrait;
    use ToggleableTrait;
    use TranslatableTrait {
        __construct as translatableConstruct;
    }

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var ShippingCategoryInterface
     */
    protected $category;

    /**
     * The one of 3 requirement variants.
     *
     * @var int
     */
    protected $categoryRequirement = ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY;

    /**
     * @var string
     */
    protected $calculator;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var Collection|RuleInterface[]
     */
    protected $rules;

    public function __construct()
    {
        $this->translatableConstruct();

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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
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
        return self::getCategoryRequirementLabels()[$this->categoryRequirement];
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
    }

    /**
     * {@inheritdoc}
     */
    public function removeRule(RuleInterface $rule)
    {
        $rule->setMethod(null);
        $this->rules->removeElement($rule);
    }

    /**
     * @return array
     */
    public static function getCategoryRequirementLabels()
    {
        return [
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE => 'None of the units have to match the method category',
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY => 'At least 1 unit has to match the method category',
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL => 'All units has to match the method category',
        ];
    }

    public function enable()
    {
        $this->enabled = true;
    }

    public function disable()
    {
        $this->enabled = false;
    }
}
