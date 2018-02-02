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

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code ?: '';
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name ?: '';
    }

    /**
     * @return CountryInterface
     */
    public function getCountry(): ?CountryInterface
    {
        return $this->country;
    }

    /**
     * @param CountryInterface $country
     */
    public function setCountry(?CountryInterface $country): void
    {
        $this->country = $country;
    }

}