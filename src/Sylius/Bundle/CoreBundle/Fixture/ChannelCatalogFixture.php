<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Webmozart\Assert\Assert;

/**
 * Makes an existing catalogue available on a second channel.
 *
 * The `channel` fixture can create a channel, but a new channel is empty: no
 * products, no prices, no payment or shipping methods. Scenarios comparing two
 * channels need both sides identical in every respect except the one under
 * test - otherwise a difference in the result may come from a difference in the
 * data rather than from the behaviour of the application.
 *
 * Runs after the default suite, so it copies what that suite created.
 */
final class ChannelCatalogFixture extends AbstractFixture
{
    public function __construct(
        private readonly ObjectManager $objectManager,
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly RepositoryInterface $productRepository,
        private readonly RepositoryInterface $productVariantRepository,
        private readonly RepositoryInterface $paymentMethodRepository,
        private readonly RepositoryInterface $shippingMethodRepository,
        private readonly FactoryInterface $channelPricingFactory,
    ) {
    }

    public function getName(): string
    {
        return 'channel_catalog';
    }

    public function load(array $options): void
    {
        $source = $this->channelRepository->findOneByCode($options['source']);
        $target = $this->channelRepository->findOneByCode($options['target']);

        Assert::isInstanceOf($source, ChannelInterface::class, sprintf('Nie ma kanalu "%s".', $options['source']));
        Assert::isInstanceOf($target, ChannelInterface::class, sprintf('Nie ma kanalu "%s".', $options['target']));

        $this->addChannelTo($this->productRepository->findAll(), $target);
        $this->addChannelTo($this->paymentMethodRepository->findAll(), $target);
        $this->addChannelTo($this->shippingMethodRepository->findAll(), $target);

        $this->copyShippingRates($source, $target);
        $this->copyPricing($source, $target);

        //  Without a menu taxon the storefront on the new channel has no
        //  category navigation: the homepage carries no taxon link at all, so
        //  scenarios walking the listing have nothing to follow.
        if (null === $target->getMenuTaxon()) {
            $target->setMenuTaxon($source->getMenuTaxon());
        }

        if ($options['disable_account_verification']) {
            $source->setAccountVerificationRequired(false);
            $target->setAccountVerificationRequired(false);
        }

        //  AbstractFixture, unlike AbstractResourceFixture, carries no manager
        //  and never flushes. Without this call the entities changed above live
        //  only in the memory of the fixture-loading process and vanish with it,
        //  leaving the shop with an empty second channel and verification on.
        $this->objectManager->flush();
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->scalarNode('source')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('target')->isRequired()->cannotBeEmpty()->end()
                //  The scenarios register customers and rely on the automatic
                //  login that follows. ChannelExampleFactory defaults to
                //  REQUIRING email verification and the default suite does not
                //  override it, so registration would leave nobody logged in
                //  and the account page would redirect to the login screen.
                ->booleanNode('disable_account_verification')->defaultTrue()->end()
            ->end()
        ;
    }

    /**
     * Attaching the channel to a shipping method is NOT enough: the `flat_rate`
     * calculator keeps the amount per channel code, and without it every
     * shipping calculation on the new channel ends in
     * MissingChannelConfigurationException - a 500 when adding to the cart that
     * reads as an application bug while being a hole in the test data.
     */
    private function copyShippingRates(ChannelInterface $source, ChannelInterface $target): void
    {
        $sourceCode = (string) $source->getCode();
        $targetCode = (string) $target->getCode();

        foreach ($this->shippingMethodRepository->findAll() as $shippingMethod) {
            $configuration = $shippingMethod->getConfiguration();

            if (!isset($configuration[$sourceCode]) || isset($configuration[$targetCode])) {
                continue;
            }

            $configuration[$targetCode] = $configuration[$sourceCode];
            $shippingMethod->setConfiguration($configuration);
        }
    }

    private function copyPricing(ChannelInterface $source, ChannelInterface $target): void
    {
        foreach ($this->productVariantRepository->findAll() as $variant) {
            if (!$variant instanceof ProductVariantInterface) {
                continue;
            }

            if (null !== $variant->getChannelPricingForChannel($target)) {
                continue;
            }

            $from = $variant->getChannelPricingForChannel($source);

            if (!$from instanceof ChannelPricingInterface) {
                continue;
            }

            /** @var ChannelPricingInterface $pricing */
            $pricing = $this->channelPricingFactory->createNew();
            $pricing->setChannelCode((string) $target->getCode());
            $pricing->setPrice($from->getPrice());
            $pricing->setOriginalPrice($from->getOriginalPrice());

            $variant->addChannelPricing($pricing);
        }
    }

    /** @param array<object> $entities */
    private function addChannelTo(array $entities, ChannelInterface $channel): void
    {
        foreach ($entities as $entity) {
            if (!method_exists($entity, 'addChannel') || !method_exists($entity, 'hasChannel')) {
                continue;
            }

            if ($entity->hasChannel($channel)) {
                continue;
            }

            $entity->addChannel($channel);
        }
    }
}
