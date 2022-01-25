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

use Sylius\Component\Locale\Model\Locale;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Locale>
 *
 * @method static Locale|Proxy createOne(array $attributes = [])
 * @method static Locale[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Locale|Proxy find(object|array|mixed $criteria)
 * @method static Locale|Proxy findOrCreate(array $attributes)
 * @method static Locale|Proxy first(string $sortedField = 'id')
 * @method static Locale|Proxy last(string $sortedField = 'id')
 * @method static Locale|Proxy random(array $attributes = [])
 * @method static Locale|Proxy randomOrCreate(array $attributes = [])
 * @method static Locale[]|Proxy[] all()
 * @method static Locale[]|Proxy[] findBy(array $attributes)
 * @method static Locale[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Locale[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method Locale|Proxy create(array|callable $attributes = [])
 */
interface LocaleFactoryInterface
{
    public function withDefaultLocaleCode(): self;

    public function withCode(string $code = null): self;
}
