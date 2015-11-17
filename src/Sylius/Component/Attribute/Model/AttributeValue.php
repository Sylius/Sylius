<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Model;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeValue implements AttributeValueInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var AttributeSubjectInterface
     */
    protected $subject;

    /**
     * @var AttributeInterface
     */
    protected $attribute;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var bool
     */
    protected $boolean;

    /**
     * @var int
     */
    protected $integer;

    /**
     * @var \DateTime
     */
    protected $dateTime;

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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(AttributeSubjectInterface $subject = null)
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute(AttributeInterface $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (null === $this->attribute) {
            return null;
        }

        $getter = 'get'.ucfirst($this->attribute->getStorageType());

        return $this->$getter();
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->assertAttributeIsSet();

        $setter = 'set'.ucfirst($this->attribute->getStorageType());
        $this->$setter($value['value']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $this->assertAttributeIsSet();

        return $this->attribute->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getPresentation()
    {
        $this->assertAttributeIsSet();

        return $this->attribute->getPresentation();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        $this->assertAttributeIsSet();

        return $this->attribute->getType();
    }

    /**
     * @return boolean
     */
    public function getBoolean()
    {
        return $this->boolean;
    }

    /**
     * @param boolean $boolean
     */
    public function setBoolean($boolean)
    {
        $this->boolean = $boolean;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getInteger()
    {
        return $this->integer;
    }

    /**
     * @param int $integer
     */
    public function setInteger($integer)
    {
        $this->integer = $integer;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $getter = 'get'.ucfirst($this->attribute->getStorageType());

        return $this->$getter();
    }

    /**
     * @throws \BadMethodCallException
     */
    protected function assertAttributeIsSet()
    {
        if (null === $this->attribute) {
            throw new \BadMethodCallException('The attribute is undefined, so you cannot access proxy methods.');
        }
    }
}
