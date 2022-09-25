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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues\PaymentMethodFactoryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer\PaymentMethodFactoryTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater\PaymentMethodFactoryUpdaterInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<PaymentMethodInterface>
 *
 * @method static PaymentMethodInterface|Proxy createOne(array $attributes = [])
 * @method static PaymentMethodInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static PaymentMethodInterface|Proxy find(object|array|mixed $criteria)
 * @method static PaymentMethodInterface|Proxy findOrCreate(array $attributes)
 * @method static PaymentMethodInterface|Proxy first(string $sortedField = 'id')
 * @method static PaymentMethodInterface|Proxy last(string $sortedField = 'id')
 * @method static PaymentMethodInterface|Proxy random(array $attributes = [])
 * @method static PaymentMethodInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static PaymentMethodInterface[]|Proxy[] all()
 * @method static PaymentMethodInterface[]|Proxy[] findBy(array $attributes)
 * @method static PaymentMethodInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static PaymentMethodInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method PaymentMethodInterface|Proxy create(array|callable $attributes = [])
 */
class PaymentMethodFactory extends ModelFactory implements PaymentMethodFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use WithDescriptionTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $paymentMethodFactory,
        private PaymentMethodFactoryDefaultValuesInterface $factoryDefaultValues,
        private PaymentMethodFactoryTransformerInterface $factoryTransformer,
        private PaymentMethodFactoryUpdaterInterface $factoryUpdater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(PaymentMethodInterface $PaymentMethod, array $attributes): void
    {
        $this->factoryUpdater->update($PaymentMethod, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): PaymentMethodInterface {
                /** @var PaymentMethodInterface $paymentMethod */
                $paymentMethod = $this->paymentMethodFactory->createNew();

                $this->update($paymentMethod, $attributes);

                return $paymentMethod;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? PaymentMethod::class;
    }
}
