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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxRateInterface;

interface ZoneInterface extends ResourceInterface, CodeAwareInterface
{
    public const TYPE_COUNTRY = 'country';

    public const TYPE_PROVINCE = 'province';

    public const TYPE_ZONE = 'zone';

    /**
     * @return string[]
     */
    public static function getTypes(): array;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getScope(): ?string;

    public function setScope(?string $scope): void;

    /**
     * @return Collection|ZoneMemberInterface[]
     *
     * @psalm-return Collection<array-key, ZoneMemberInterface>
     */
    public function getMembers(): Collection;

    public function hasMembers(): bool;

    public function addMember(ZoneMemberInterface $member): void;

    public function removeMember(ZoneMemberInterface $member): void;

    public function hasMember(ZoneMemberInterface $member): bool;

    public function getShippingMethods(): Collection;

    public function hasShippingMethods(): bool;

    public function addShippingMethod(ShippingMethodInterface $shippingMethod): void;

    public function removeShippingMethod(ShippingMethodInterface $shippingMethod): void;

    public function hasShippingMethod(ShippingMethodInterface $shippingMethod): bool;

    public function getTaxRates(): Collection;

    public function hasTaxRates(): bool;

    public function addTaxRate(TaxRateInterface $taxRate): void;

    public function removeTaxRate(TaxRateInterface $taxRate): void;

    public function hasTaxRate(TaxRateInterface $taxRate): bool;
}
