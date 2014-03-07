<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customization\Model;

/**
 * Customization value.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CustomizationValue implements CustomizationValueInterface
{
    /**
     * Customization value id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Value.
     *
     * @var string
     */
    protected $value;

    /**
     * Customization.
     *
     * @var CustomizationInterface
     */
    protected $customization;

    /**
     * Customization subject instance
     *
     * @var CustomizationSubjectInstanceInterface
     */
    protected $subjectInstance;

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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomization()
    {
        return $this->customization;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomization(CustomizationInterface $customization = null)
    {
        $this->customization = $customization;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectInstance()
    {
        return $this->subjectInstance;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubjectInstance(CustomizationSubjectInstanceInterface $subjectInstance = null)
    {
        $this->subjectInstance = $subjectInstance;
    }
}
