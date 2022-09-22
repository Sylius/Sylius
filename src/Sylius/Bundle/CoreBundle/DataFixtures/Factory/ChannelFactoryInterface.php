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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ChannelInterface>
 *
 * @method static ChannelInterface|Proxy createOne(array $attributes = [])
 * @method static ChannelInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ChannelInterface|Proxy find(object|array|mixed $criteria)
 * @method static ChannelInterface|Proxy findOrCreate(array $attributes)
 * @method static ChannelInterface|Proxy first(string $sortedField = 'id')
 * @method static ChannelInterface|Proxy last(string $sortedField = 'id')
 * @method static ChannelInterface|Proxy random(array $attributes = [])
 * @method static ChannelInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ChannelInterface[]|Proxy[] all()
 * @method static ChannelInterface[]|Proxy[] findBy(array $attributes)
 * @method static ChannelInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ChannelInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ChannelInterface|Proxy create(array|callable $attributes = [])
 */
interface ChannelFactoryInterface extends WithCodeInterface, WithNameInterface, ToggableInterface
{
    public function withHostname(string $hostname): self;

    public function withColor(string $color): self;

    public function withSkippingShippingStepAllowed(): self;

    public function withSkippingPaymentStepAllowed(): self;

    public function withoutAccountVerificationRequired(): self;

    public function withDefaultTaxZone(Proxy|ZoneInterface|string $defaultTaxZone): self;

    public function withTaxCalculationStrategy(string $taxCalculationStrategy): self;

    public function withThemeName(string $themeName): self;

    public function withContactEmail(string $contactEmail): self;

    public function withContactPhoneNumber(string $contactPhoneNumber): self;
}
