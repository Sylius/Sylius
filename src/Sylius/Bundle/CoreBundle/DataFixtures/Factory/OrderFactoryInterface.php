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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<OrderInterface>
 *
 * @method static OrderInterface|Proxy createOne(array $attributes = [])
 * @method static OrderInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static OrderInterface|Proxy find(object|array|mixed $criteria)
 * @method static OrderInterface|Proxy findOrCreate(array $attributes)
 * @method static OrderInterface|Proxy first(string $sortedField = 'id')
 * @method static OrderInterface|Proxy last(string $sortedField = 'id')
 * @method static OrderInterface|Proxy random(array $attributes = [])
 * @method static OrderInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static OrderInterface[]|Proxy[] all()
 * @method static OrderInterface[]|Proxy[] findBy(array $attributes)
 * @method static OrderInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static OrderInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method OrderInterface|Proxy create(array|callable $attributes = [])
 */
interface OrderFactoryInterface extends WithChannelInterface, WithCustomerInterface, WithCountryInterface
{
}
