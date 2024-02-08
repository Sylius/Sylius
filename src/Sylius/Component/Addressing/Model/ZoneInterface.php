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

namespace Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

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
     * @return Collection<array-key, ZoneMemberInterface>
     */
    public function getMembers(): Collection;

    public function hasMembers(): bool;

    public function addMember(ZoneMemberInterface $member): void;

    public function removeMember(ZoneMemberInterface $member): void;

    public function hasMember(ZoneMemberInterface $member): bool;
}
