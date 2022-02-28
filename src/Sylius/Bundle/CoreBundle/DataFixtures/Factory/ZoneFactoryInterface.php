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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ZoneInterface>
 *
 * @method static ZoneInterface|Proxy createOne(array $attributes = [])
 * @method static ZoneInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ZoneInterface|Proxy find(object|array|mixed $criteria)
 * @method static ZoneInterface|Proxy findOrCreate(array $attributes)
 * @method static ZoneInterface|Proxy first(string $sortedField = 'id')
 * @method static ZoneInterface|Proxy last(string $sortedField = 'id')
 * @method static ZoneInterface|Proxy random(array $attributes = [])
 * @method static ZoneInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ZoneInterface[]|Proxy[] all()
 * @method static ZoneInterface[]|Proxy[] findBy(array $attributes)
 * @method static ZoneInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ZoneInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ZoneInterface|Proxy create(array|callable $attributes = [])
 */
interface ZoneFactoryInterface
{
    public function withCode(string $code): self;

    public function withMembers(array $members, string $type): self;

    public function withCountries(array $countries): self;

    public function withProvinces(array $countries): self;

    public function withScope(string $scope): self;
}
