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

use Sylius\Component\Customer\Model\CustomerGroup;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CustomerGroup>
 *
 * @method static CustomerGroup|Proxy createOne(array $attributes = [])
 * @method static CustomerGroup[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CustomerGroup|Proxy find(object|array|mixed $criteria)
 * @method static CustomerGroup|Proxy findOrCreate(array $attributes)
 * @method static CustomerGroup|Proxy first(string $sortedField = 'id')
 * @method static CustomerGroup|Proxy last(string $sortedField = 'id')
 * @method static CustomerGroup|Proxy random(array $attributes = [])
 * @method static CustomerGroup|Proxy randomOrCreate(array $attributes = [])
 * @method static CustomerGroup[]|Proxy[] all()
 * @method static CustomerGroup[]|Proxy[] findBy(array $attributes)
 * @method static CustomerGroup[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CustomerGroup[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CustomerGroup|Proxy create(array|callable $attributes = [])
 */
interface CustomerGroupFactoryInterface
{
    public function withCode(string $code): self;

    public function withName(string $name): self;
}
