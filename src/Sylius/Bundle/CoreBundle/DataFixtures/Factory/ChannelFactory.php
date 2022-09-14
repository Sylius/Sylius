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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues\ChannelFactoryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer\ChannelFactoryTransformerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface as ChannelResourceFactory;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
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
    public function __construct(
        private ChannelResourceFactory $channelFactory,
        private ChannelFactoryDefaultValuesInterface $factoryDefaultValues,
        private ChannelFactoryTransformerInterface $factoryTransformer,
    ) {
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

    public function withHostname(string $hostname): self
    {
        return $this->addState(['hostname' => $hostname]);
    }

    public function withColor(string $color): self
    {
        return $this->addState(['color' => $color]);
    }

    public function enabled(): self
    {
        return $this->addState(['enabled' => true]);
    }

    public function disabled(): self
    {
        return $this->addState(['enabled' => false]);
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

    public function withThemeName(string $themeName): self
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

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
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

                $channel->setCode($attributes['code']);
                $channel->setHostname($attributes['hostname']);
                $channel->setEnabled($attributes['enabled']);
                $channel->setColor($attributes['color']);
                $channel->setDefaultTaxZone($attributes['default_tax_zone']);
                $channel->setTaxCalculationStrategy($attributes['tax_calculation_strategy']);
                $channel->setThemeName($attributes['theme_name']);
                $channel->setContactEmail($attributes['contact_email']);
                $channel->setContactPhoneNumber($attributes['contact_phone_number']);
                $channel->setSkippingShippingStepAllowed($attributes['skipping_shipping_step_allowed']);
                $channel->setSkippingPaymentStepAllowed($attributes['skipping_payment_step_allowed']);
                $channel->setAccountVerificationRequired($attributes['account_verification_required']);
                $channel->setMenuTaxon($attributes['menu_taxon']);

                $channel->setDefaultLocale($attributes['default_locale']);
                foreach ($attributes['locales'] as $locale) {
                    $channel->addLocale($locale);
                }

                $channel->setBaseCurrency($attributes['base_currency']);
                foreach ($attributes['currencies'] as $currency) {
                    $channel->addCurrency($currency);
                }

                if (null !== $attributes['shop_billing_data']) {
                    $channel->setShopBillingData($attributes['shop_billing_data']);
                }

                return $channel;
            })
        ;
    }

    protected static function getClass(): string
    {
        return Channel::class;
    }
}
