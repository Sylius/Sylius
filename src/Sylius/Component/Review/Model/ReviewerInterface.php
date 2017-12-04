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

namespace Sylius\Component\Review\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ReviewerInterface extends ResourceInterface
{
    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string;

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void;

    /**
     * @return string|null
     */
    public function getLastName(): ?string;

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void;
}
