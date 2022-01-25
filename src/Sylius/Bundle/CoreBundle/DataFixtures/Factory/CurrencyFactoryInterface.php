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

use Sylius\Component\Currency\Model\Currency;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Currency>
 *
 * @method static Currency|Proxy createOne(array $attributes = [])
 * @method static Currency[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Currency|Proxy find(object|array|mixed $criteria)
 * @method static Currency|Proxy findOrCreate(array $attributes)
 * @method static Currency|Proxy first(string $sortedField = 'id')
 * @method static Currency|Proxy last(string $sortedField = 'id')
 * @method static Currency|Proxy random(array $attributes = [])
 * @method static Currency|Proxy randomOrCreate(array $attributes = [])
 * @method static Currency[]|Proxy[] all()
 * @method static Currency[]|Proxy[] findBy(array $attributes)
 * @method static Currency[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Currency[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method Currency|Proxy create(array|callable $attributes = [])
 */
interface CurrencyFactoryInterface
{
    public function withCode(string $code = null): self;
}
