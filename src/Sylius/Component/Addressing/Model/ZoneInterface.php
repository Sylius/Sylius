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

interface ZoneInterface extends ResourceInterface, CodeAwareInterface
{
    public const TYPE_COUNTRY = 'country';
    public const TYPE_PROVINCE = 'province';
    public const TYPE_ZONE = 'zone';

    /**
     * @return string[]
     */
    public static function getTypes(): array;

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
    public function getType(): ?string;

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void;

    /**
     * @return string|null
     */
    public function getScope(): ?string;

    /**
     * @param string|null $scope
     */
    public function setScope(?string $scope): void;

    /**
     * @return Collection|ZoneMemberInterface[]
     */
    public function getMembers(): Collection;

    /**
     * @return bool
     */
    public function hasMembers(): bool;

    /**
     * @param ZoneMemberInterface $member
     */
    public function addMember(ZoneMemberInterface $member): void;

    /**
     * @param ZoneMemberInterface $member
     */
    public function removeMember(ZoneMemberInterface $member): void;

    /**
     * @param ZoneMemberInterface $member
     *
     * @return bool
     */
    public function hasMember(ZoneMemberInterface $member): bool;
}
