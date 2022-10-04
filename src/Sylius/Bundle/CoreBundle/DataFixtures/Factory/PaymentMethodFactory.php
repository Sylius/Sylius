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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\PaymentMethodDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\ToggableTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithChannelsTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCodeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithDescriptionTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithNameTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\PaymentMethodTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\PaymentMethodUpdaterInterface;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface as ResourceFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Core\Model\PaymentMethodInterface;
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
    use ToggableTrait;
    use WithChannelsTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private ResourceFactoryInterface $paymentMethodFactory,
        private PaymentMethodDefaultValuesInterface $defaultValues,
        private PaymentMethodTransformerInterface $transformer,
        private PaymentMethodUpdaterInterface $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withInstructions(string $instructions): self
    {
        return $this->addState(['instructions' => $instructions]);
    }

    public function withGatewayName(string $gatewayName): self
    {
        return $this->addState(['gateway_name' => $gatewayName]);
    }

    public function withGatewayFactory(string $gatewayFactory): self
    {
        return $this->addState(['gateway_factory' => $gatewayFactory]);
    }

    public function withGatewayConfig(array $gatewayConfig): self
    {
        return $this->addState(['gateway_config' => $gatewayConfig]);
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(PaymentMethodInterface $PaymentMethod, array $attributes): void
    {
        $this->updater->update($PaymentMethod, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): PaymentMethodInterface {
                $paymentMethod = $this->paymentMethodFactory->createWithGateway($attributes['gateway_factory']);

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
