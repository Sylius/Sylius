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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\ToggableInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithChannelsInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCodeInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithDescriptionInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithNameInterface;
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
interface PaymentMethodFactoryInterface extends WithCodeInterface, WithNameInterface, WithDescriptionInterface, ToggableInterface, WithChannelsInterface
{
    public function withInstructions(string $instructions): self;

    public function withGatewayName(string $gatewayName): self;

    public function withGatewayFactory(string $gatewayFactory): self;

    public function withGatewayConfig(array $gatewayConfig): self;
}
