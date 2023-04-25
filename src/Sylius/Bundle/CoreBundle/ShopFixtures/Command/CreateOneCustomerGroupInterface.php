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

interface CreateOneCustomerGroupInterface extends CommandInterface
{
    /**
     * @return $this
     */
    public function withCode(string $code): self;

    /**
     * @return $this
     */
    public function withName(string $name): self;
}
