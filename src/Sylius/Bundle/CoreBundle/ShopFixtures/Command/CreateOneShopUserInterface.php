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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Command;

interface CreateOneShopUserInterface extends CommandInterface
{
    /**
     * @return $this
     */
    public function withEmail(string $email): self;

    /**
     * @return $this
     */
    public function withFirstName(string $firstName): self;

    /**
     * @return $this
     */
    public function withLastName(string $lastName): self;

    /**
     * @return $this
     */
    public function male(): self;

    /**
     * @return $this
     */
    public function female(): self;

    /**
     * @return $this
     */
    public function withPhoneNumber(string $phoneNumber): self;

    /**
     * @return $this
     */
    public function withBirthday(\DateTimeInterface|string $birthday): self;

    /**
     * @return $this
     */
    public function withPassword(string $password): self;
}
