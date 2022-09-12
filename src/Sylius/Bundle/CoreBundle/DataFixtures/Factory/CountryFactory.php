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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CountryInterface>
 *
 * @method static CountryInterface|Proxy createOne(array $attributes = [])
 * @method static CountryInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CountryInterface|Proxy find(object|array|mixed $criteria)
 * @method static CountryInterface|Proxy findOrCreate(array $attributes)
 * @method static CountryInterface|Proxy first(string $sortedField = 'id')
 * @method static CountryInterface|Proxy last(string $sortedField = 'id')
 * @method static CountryInterface|Proxy random(array $attributes = [])
 * @method static CountryInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CountryInterface[]|Proxy[] all()
 * @method static CountryInterface[]|Proxy[] findBy(array $attributes)
 * @method static CountryInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CountryInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CountryInterface|Proxy create(array|callable $attributes = [])
 */
class CountryFactory extends ModelFactory implements CountryFactoryInterface, FactoryWithModelClassAwareInterface
{
    private static string $modelClass;

    public function __construct(private FactoryInterface $countryFactory)
    {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }

    public function enabled(): self
    {
        return $this->addState(['enabled' => true]);
    }

    public function disabled(): self
    {
        return $this->addState(['enabled' => false]);
    }

    protected function getDefaults(): array
    {
        return [
            'code' => self::faker()->unique()->countryCode(),
            'enabled' => self::faker()->boolean(80),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): CountryInterface {
                /** @var Country $country */
                $country = $this->countryFactory->createNew();

                $country->setCode($attributes['code']);
                $country->setEnabled($attributes['enabled']);

                return $country;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Country::class;
    }
}
