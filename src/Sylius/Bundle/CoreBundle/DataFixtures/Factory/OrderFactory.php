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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\OrderDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithChannelTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCountryTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCustomerTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\OrderTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\OrderUpdaterInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<OrderInterface>
 *
 * @method static OrderInterface|Proxy createOne(array $attributes = [])
 * @method static OrderInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static OrderInterface|Proxy find(object|array|mixed $criteria)
 * @method static OrderInterface|Proxy findOrCreate(array $attributes)
 * @method static OrderInterface|Proxy first(string $sortedField = 'id')
 * @method static OrderInterface|Proxy last(string $sortedField = 'id')
 * @method static OrderInterface|Proxy random(array $attributes = [])
 * @method static OrderInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static OrderInterface[]|Proxy[] all()
 * @method static OrderInterface[]|Proxy[] findBy(array $attributes)
 * @method static OrderInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static OrderInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method OrderInterface|Proxy create(array|callable $attributes = [])
 */
class OrderFactory extends ModelFactory implements OrderFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithChannelTrait;
    use WithCustomerTrait;
    use WithCountryTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface            $orderFactory,
        private OrderDefaultValuesInterface $defaultValues,
        private OrderTransformerInterface $transformer,
        private OrderUpdaterInterface       $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(OrderInterface $order, array $attributes): void
    {
        $this->updater->update($order, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): OrderInterface {
                /** @var OrderInterface $order */
                $order = $this->orderFactory->createNew();

                $this->update($order, $attributes);

                return $order;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Order::class;
    }
}
