<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 01/02/18
 * Time: 11:21
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Model;


class PostCode implements PostCodeInterface
{
    /** @var int|null */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $postCode;

    /** @var CountryInterface */
    protected $country;

    /** {@inheritdoc} */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /** {@inheritdoc} */
    public function getCode(): string
    {
        $countryCode = $this->country === null ? '  ' : $this->country->getCode();
        return "{$countryCode}-{$this->postCode}";
    }

    public function setCode(?string $code): void
    {
        //do not call this method as it does not have any effect
    }

    /** {@inheritdoc} */
    public function getName(): ?string
    {
        return $this->name ?? '';
    }

    /** {@inheritdoc} */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /** {@inheritdoc} */
    public function getCountry(): ?CountryInterface
    {
        return $this->country;
    }

    /** {@inheritdoc} */
    public function setCountry(?CountryInterface $country): void
    {
        $this->country = $country;
    }

    /** {@inheritdoc} */
    public function getPostCode(): string
    {
        return $this->postCode ?? '';
    }

    /**
     * @param null|string $postCode
     */
    public function setPostCode(?string $postCode): void
    {
        $this->postCode = $postCode;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getName();
    }
}