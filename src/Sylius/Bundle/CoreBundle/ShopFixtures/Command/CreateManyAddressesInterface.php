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

interface CreateManyAddressesInterface extends CommandInterface, CreateManyInterface
{
    /**
     * @return $this
     */
    public function withCompany(string $company): self;

    /**
     * @return $this
     */
    public function withStreet(string $street): self;

    /**
     * @return $this
     */
    public function withCity(string $city): self;
}
