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
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CurrencyInterface>
 *
 * @method static CurrencyInterface|Proxy createOne(array $attributes = [])
 * @method static CurrencyInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CurrencyInterface|Proxy find(object|array|mixed $criteria)
 * @method static CurrencyInterface|Proxy findOrCreate(array $attributes)
 * @method static CurrencyInterface|Proxy first(string $sortedField = 'id')
 * @method static CurrencyInterface|Proxy last(string $sortedField = 'id')
 * @method static CurrencyInterface|Proxy random(array $attributes = [])
 * @method static CurrencyInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CurrencyInterface[]|Proxy[] all()
 * @method static CurrencyInterface[]|Proxy[] findBy(array $attributes)
 * @method static CurrencyInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CurrencyInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CurrencyInterface|Proxy create(array|callable $attributes = [])
 */
final class CurrencyFactory extends ModelFactory implements CurrencyFactoryInterface
{
    public function __construct(private FactoryInterface $currencyFactory)
    {
        parent::__construct();
    }

    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }

    protected function getDefaults(): array
    {
        return [
            'code' => self::faker()->unique()->currencyCode(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): CurrencyInterface {
                /** @var CurrencyInterface $currency */
                $currency = $this->currencyFactory->createNew();

                $currency->setCode($attributes['code']);

                return $currency;
            })
        ;
    }

    protected static function getClass(): string
    {
        return Currency::class;
    }
}
