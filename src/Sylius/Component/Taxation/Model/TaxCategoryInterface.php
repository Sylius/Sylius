<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Taxation\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface TaxCategoryInterface extends CodeAwareInterface, TimestampableInterface, ResourceInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    /**
     * @return Collection<array-key, TaxRateInterface>
     */
    public function getRates(): Collection;

    public function addRate(TaxRateInterface $rate): void;

    public function removeRate(TaxRateInterface $rate): void;

    public function hasRate(TaxRateInterface $rate): bool;
}
