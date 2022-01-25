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

use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Customer\Model\CustomerGroup;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
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
final class CustomerGroupFactory extends ModelFactory implements CustomerGroupFactoryInterface
{
    public function __construct(private FactoryInterface $CustomerGroupFactory)
    {
        parent::__construct();
    }

    public function withCode(string $code = null): self
    {
        return $this->addState(function () use ($code) {
            return ['code' => $code ?? StringInflector::nameToCode(self::faker()->words(3, true))];
        });
    }

    public function withName(string $name = null): self
    {
        return $this->addState(function () use ($name) {
            return ['name' => $name ?? self::faker()->words(3, true)];
        });
    }

    protected function getDefaults(): array
    {
        return [
            'code' => null,
            'name' => self::faker()->words(3, true),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                $attributes['code'] = $attributes['code'] ?: StringInflector::nameToCode($attributes['name']);

                return $attributes;
            })
            ->instantiateWith(function(array $attributes): CustomerGroupInterface {
                /** @var CustomerGroupInterface $customerGroup */
                $customerGroup = $this->CustomerGroupFactory->createNew();

                $customerGroup->setCode($attributes['code']);
                $customerGroup->setName($attributes['name']);

                return $customerGroup;
            })
        ;
    }

    protected static function getClass(): string
    {
        return CustomerGroup::class;
    }
}
