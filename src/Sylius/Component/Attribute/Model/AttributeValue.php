<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Attribute\Model;

use Webmozart\Assert\Assert;

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
     * @var string
     */
    protected $localeCode;

    /**
     * @var string
     */
    private $text;

    /**
     * @var bool
     */
    private $boolean;

    /**
     * @var int
     */
    private $integer;

    /**
     * @var float
     */
    private $float;

    /**
     * @var \DateTimeInterface
     */
    private $datetime;

    /**
     * @var \DateTimeInterface
     */
    private $date;

    /**
     * @var array
     */
    private $json;

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
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocaleCode($localeCode)
    {
        Assert::string($localeCode);

        $this->localeCode = $localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (null === $this->attribute) {
            return null;
        }

        $getter = 'get' . $this->attribute->getStorageType();

        return $this->$getter();
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->assertAttributeIsSet();

        $setter = 'set' . $this->attribute->getStorageType();

        $this->$setter($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        $this->assertAttributeIsSet();

        return $this->attribute->getCode();
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
    public function getType()
    {
        $this->assertAttributeIsSet();

        return $this->attribute->getType();
    }

    /**
     * @return bool|null
     */
    protected function getBoolean()
    {
        return $this->boolean;
    }

    /**
     * @param bool|null $boolean
     */
    protected function setBoolean($boolean)
    {
        $this->boolean = $boolean;
    }

    /**
     * @return string|null
     */
    protected function getText()
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    protected function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return int|null
     */
    protected function getInteger()
    {
        return $this->integer;
    }

    /**
     * @param int|null $integer
     */
    protected function setInteger($integer)
    {
        $this->integer = $integer;
    }

    /**
     * @return float|null
     */
    protected function getFloat()
    {
        return $this->float;
    }

    /**
     * @param float|null $float
     */
    protected function setFloat($float)
    {
        $this->float = $float;
    }

    /**
     * @return \DateTimeInterface|null
     */
    protected function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param \DateTimeInterface|null $datetime
     */
    protected function setDatetime(\DateTimeInterface $datetime = null)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return \DateTimeInterface|null
     */
    protected function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface|null $date
     */
    protected function setDate(\DateTimeInterface $date = null)
    {
        $this->date = $date;
    }

    /**
     * @return array|null
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param array|null $json
     */
    public function setJson(array $json = null)
    {
        $this->json = $json;
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
