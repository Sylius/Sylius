<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration;

use Sylius\Component\Attribute\Model\AttributeInterface;

/**
 * @author Salvatore Pappalardo <salvatore.pappalardo82@gmail.com>
 */
class AttributeValueTypeConfiguration implements AttributeValueTypeConfigurationInterface
{
    /**
     * @var AttributeInterface
     */
    protected $attribute;

    /**
     * @var string
     */
    protected $subjectName;

    /**
     * @var int
     */
    protected $counter;

    /**
     * @param AttributeInterface $attribute
     * @param string $subjectName
     * @param int $counter
     */
    public function __construct(AttributeInterface $attribute, $subjectName, $counter)
    {
        $this->attribute = $attribute;
        $this->subjectName = $subjectName;
        $this->counter = $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'value';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->attribute->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'sylius_attribute_type_'.$this->attribute->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions()
    {
        return ['label' => $this->getLabel()];
    }
}
