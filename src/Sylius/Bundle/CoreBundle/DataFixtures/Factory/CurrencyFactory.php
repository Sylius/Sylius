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
final class CurrencyFactory extends ModelFactory implements CurrencyFactoryInterface
{
    public function __construct(private FactoryInterface $currencyFactory)
    {
        parent::__construct();
    }

    public function withCode(string $code = null): self
    {
        return $this->addState(function () use ($code) {
            return ['code' => $code ?? self::faker()->unique()->currencyCode()];
        });
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
