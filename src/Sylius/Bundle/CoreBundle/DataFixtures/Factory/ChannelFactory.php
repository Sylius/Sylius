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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\ChannelDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\ChannelTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\ChannelUpdaterInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface as ChannelResourceFactory;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Core\Model\TaxonInterface;
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
class ChannelFactory extends ModelFactory implements ChannelFactoryInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use ToggableTrait;
    use WithLocalesTrait;
    use WithCurrenciesTrait;

    public function __construct(
        private ChannelResourceFactory        $channelFactory,
        private ChannelDefaultValuesInterface $factoryDefaultValues,
        private ChannelTransformerInterface   $factoryTransformer,
        private ChannelUpdaterInterface       $factoryUpdater,
    ) {
        parent::__construct();
    }

    public function withHostname(string $hostname): self
    {
        return $this->addState(['hostname' => $hostname]);
    }

    public function withColor(string $color): self
    {
        return $this->addState(['color' => $color]);
    }

    public function withSkippingShippingStepAllowed(): self
    {
        return $this->addState(['skipping_shipping_step_allowed' => true]);
    }

    public function withSkippingPaymentStepAllowed(): self
    {
        return $this->addState(['skipping_payment_step_allowed' => true]);
    }

    public function withoutAccountVerificationRequired(): self
    {
        return $this->addState(['account_verification_required' => false]);
    }

    public function withDefaultTaxZone(Proxy|ZoneInterface|string $defaultTaxZone): self
    {
        return $this->addState(['default_tax_zone' => $defaultTaxZone]);
    }

    public function withTaxCalculationStrategy(string $taxCalculationStrategy): self
    {
        return $this->addState(['tax_calculation_strategy' => $taxCalculationStrategy]);
    }

    public function withThemeName(?string $themeName): self
    {
        return $this->addState(['theme_name' => $themeName]);
    }

    public function withContactEmail(string $contactEmail): self
    {
        return $this->addState(['contact_email' => $contactEmail]);
    }

    public function withContactPhoneNumber(string $contactPhoneNumber): self
    {
        return $this->addState(['contact_phone_number' => $contactPhoneNumber]);
    }

    public function withShopBillingData(Proxy|ShopBillingDataInterface|array $shopBillingData): self
    {
        return $this->addState(['shop_billing_data' => $shopBillingData]);
    }

    public function withMenuTaxon(Proxy|TaxonInterface|string $menuTaxon): self
    {
        return $this->addState(['menu_taxon' => $menuTaxon]);
    }

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(ChannelInterface $channel, array $attributes): void
    {
        $this->factoryUpdater->update($channel, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): ChannelInterface {
                /** @var ChannelInterface $channel */
                $channel = $this->channelFactory->createNamed($attributes['name']);

                return $channel;
            })
            ->afterInstantiate(function (ChannelInterface $channel, array $attributes): void {
                $this->update($channel, $attributes);
            })
        ;
    }

    protected static function getClass(): string
    {
        return Channel::class;
    }
}
