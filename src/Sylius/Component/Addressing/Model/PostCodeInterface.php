<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 01/02/18
 * Time: 11:33
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface PostCodeInterface extends ResourceInterface
{
    /**
     * @param int|null $id
     */
    public function setId(?int $id): void;

    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return string
     */
    public function getName(): ?string;

    /**
     * @param string $name
     */
    public function setName(?string $name): void;

    /**
     * @return string
     */
    public function getPostCode(): string;

    /**
     * @param null|string $postCode
     */
    public function setPostCode(?string $postCode): void;

    /**
     * @return CountryInterface
     */
    public function getCountry(): ?CountryInterface;

    /**
     * @param CountryInterface $country
     */
    public function setCountry(?CountryInterface $country): void;
}