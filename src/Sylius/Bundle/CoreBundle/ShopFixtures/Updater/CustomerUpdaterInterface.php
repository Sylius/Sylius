<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Updater;

use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerUpdaterInterface
{
    public function update(CustomerInterface $customer, array $attributes): void;
}
