<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 01/02/18
 * Time: 11:33
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Model;

interface PostalCodeInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void;

    /**
     * @return string
     */
    public function getCode(): ?string;

    /**
     * @param string $code
     */
    public function setCode(string $code): void;

    /**
     * @return string
     */
    public function getName(): ?string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * @return CountryInterface
     */
    public function getCountry(): CountryInterface;

    /**
     * @param CountryInterface $country
     */
    public function setCountry(CountryInterface $country): void;
}