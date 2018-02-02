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

namespace Sylius\Component\Addressing\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ProvinceInterface extends ResourceInterface, CodeAwareInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getAbbreviation(): ?string;

    /**
     * @param string|null $abbreviation
     */
    public function setAbbreviation(?string $abbreviation): void;

    /**
     * @return CountryInterface|null
     */
    public function getCountry(): ?CountryInterface;

    /**
     * @param CountryInterface|null $country
     */
    public function setCountry(?CountryInterface $country): void;
}
