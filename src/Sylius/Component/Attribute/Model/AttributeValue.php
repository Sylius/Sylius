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

class AttributeValue implements AttributeValueInterface
{
    /** @var mixed */
    protected $id;

    /** @var AttributeSubjectInterface */
    protected $subject;

    /** @var AttributeInterface */
    protected $attribute;

    /** @var string */
    protected $localeCode;

    /** @var string */
    private $text;

    /** @var bool */
    private $boolean;

    /** @var int */
    private $integer;

    /** @var float */
    private $float;

    /** @var \DateTimeInterface */
    private $datetime;

    /** @var \DateTimeInterface */
    private $date;

    /** @var array */
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
    public function getSubject(): ?AttributeSubjectInterface
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(?AttributeSubjectInterface $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(): ?AttributeInterface
    {
        return $this->attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute(?AttributeInterface $attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocaleCode(?string $localeCode): void
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
    public function setValue($value): void
    {
        $this->assertAttributeIsSet();

        $setter = 'set' . $this->attribute->getStorageType();

        $this->$setter($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        $this->assertAttributeIsSet();

        return $this->attribute->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        $this->assertAttributeIsSet();

        return $this->attribute->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ?string
    {
        $this->assertAttributeIsSet();

        return $this->attribute->getType();
    }

    protected function getBoolean(): ?bool
    {
        return $this->boolean;
    }

    protected function setBoolean(?bool $boolean): void
    {
        $this->boolean = $boolean;
    }

    protected function getText(): ?string
    {
        return $this->text;
    }

    protected function setText(?string $text): void
    {
        $this->text = $text;
    }

    protected function getInteger(): ?int
    {
        return $this->integer;
    }

    protected function setInteger(?int $integer): void
    {
        $this->integer = $integer;
    }

    protected function getFloat(): ?float
    {
        return $this->float;
    }

    protected function setFloat(?float $float): void
    {
        $this->float = $float;
    }

    protected function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    /**
     * @param \DateTimeInterface $datetime
     */
    protected function setDatetime(?\DateTimeInterface $datetime): void
    {
        $this->datetime = $datetime;
    }

    protected function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    protected function setDate(?\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    protected function getJson(): ?array
    {
        return $this->json;
    }

    protected function setJson(?array $json): void
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
