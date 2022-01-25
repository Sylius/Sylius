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

use Sylius\Component\Addressing\Model\Country;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Country>
 *
 * @method static Country|Proxy createOne(array $attributes = [])
 * @method static Country[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Country|Proxy find(object|array|mixed $criteria)
 * @method static Country|Proxy findOrCreate(array $attributes)
 * @method static Country|Proxy first(string $sortedField = 'id')
 * @method static Country|Proxy last(string $sortedField = 'id')
 * @method static Country|Proxy random(array $attributes = [])
 * @method static Country|Proxy randomOrCreate(array $attributes = [])
 * @method static Country[]|Proxy[] all()
 * @method static Country[]|Proxy[] findBy(array $attributes)
 * @method static Country[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Country[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method Country|Proxy create(array|callable $attributes = [])
 */
interface CountryFactoryInterface
{
    public function withCode(string $code = null): self;

    public function enabled(): self;

    public function disabled(): self;
}
