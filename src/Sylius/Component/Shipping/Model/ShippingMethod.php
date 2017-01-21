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

use Sylius\Component\Resource\Model\ArchivableTrait;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ShippingMethod implements ShippingMethodInterface
{
    use ArchivableTrait, TimestampableTrait, ToggleableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
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
     * @var int
     */
    protected $position;

    /**
     * @var ShippingCategoryInterface
     */
    protected $category;

    /**
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

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTranslation()->__toString();
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
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
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
        return $this->getTranslation()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getTranslation()->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->getTranslation()->setDescription($description);
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

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new ShippingMethodTranslation();
    }
}
