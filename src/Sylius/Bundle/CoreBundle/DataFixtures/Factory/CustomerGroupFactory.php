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
 * @extends ModelFactory<CustomerGroupInterface>
 *
 * @method static CustomerGroupInterface|Proxy createOne(array $attributes = [])
 * @method static CustomerGroupInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CustomerGroupInterface|Proxy find(object|array|mixed $criteria)
 * @method static CustomerGroupInterface|Proxy findOrCreate(array $attributes)
 * @method static CustomerGroupInterface|Proxy first(string $sortedField = 'id')
 * @method static CustomerGroupInterface|Proxy last(string $sortedField = 'id')
 * @method static CustomerGroupInterface|Proxy random(array $attributes = [])
 * @method static CustomerGroupInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CustomerGroupInterface[]|Proxy[] all()
 * @method static CustomerGroupInterface[]|Proxy[] findBy(array $attributes)
 * @method static CustomerGroupInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CustomerGroupInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CustomerGroupInterface|Proxy create(array|callable $attributes = [])
 */
final class CustomerGroupFactory extends ModelFactory implements CustomerGroupFactoryInterface
{
    public function __construct(private FactoryInterface $CustomerGroupFactory)
    {
        parent::__construct();
    }

    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }

    public function withName(string $name): self
    {
        return $this->addState(['name' => $name]);
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
