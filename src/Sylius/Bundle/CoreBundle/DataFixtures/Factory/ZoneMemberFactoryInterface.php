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

use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ZoneMemberInterface>
 *
 * @method static ZoneMemberInterface|Proxy createOne(array $attributes = [])
 * @method static ZoneMemberInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ZoneMemberInterface|Proxy find(object|array|mixed $criteria)
 * @method static ZoneMemberInterface|Proxy findOrCreate(array $attributes)
 * @method static ZoneMemberInterface|Proxy first(string $sortedField = 'id')
 * @method static ZoneMemberInterface|Proxy last(string $sortedField = 'id')
 * @method static ZoneMemberInterface|Proxy random(array $attributes = [])
 * @method static ZoneMemberInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ZoneMemberInterface[]|Proxy[] all()
 * @method static ZoneMemberInterface[]|Proxy[] findBy(array $attributes)
 * @method static ZoneMemberInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ZoneMemberInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ZoneMemberInterface|Proxy create(array|callable $attributes = [])
 */
interface ZoneMemberFactoryInterface
{
    public function withCode(string $code): self;
}
