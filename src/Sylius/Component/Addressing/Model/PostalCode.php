<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 01/02/18
 * Time: 11:21
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Model;


class PostalCode implements PostalCodeInterface
{
    /** @var int|null */
    protected $id;

    /** @var string */
    protected $code;

    /** @var string */
    protected $name;

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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /** {@inheritdoc} */
    public function setCode(?string $code): void
    {
        $this->code = $code ?: '';
    }

    /** {@inheritdoc} */
    public function getName(): ?string
    {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function setName(?string $name): void
    {
        $this->name = $name ?: '';
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

}