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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingMethodExampleFactory;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Scope as CoreScope;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalGreaterThanOrEqualRuleChecker;
use Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalLessThanOrEqualRuleChecker;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\Checker\Rule\TotalWeightGreaterThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Checker\Rule\TotalWeightLessThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

final class ShippingContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ShippingMethodRepositoryInterface $shippingMethodRepository,
        private RepositoryInterface $zoneRepository,
        private ShippingMethodExampleFactory $shippingMethodExampleFactory,
        private FactoryInterface $shippingMethodRuleFactory,
        private ObjectManager $shippingMethodManager,
    ) {
    }

    /**
     * @Given the store ships everything for Free within the :zone zone
     */
    public function storeShipsEverythingForFree(ZoneInterface $zone): void
    {
        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => 'Free',
            'enabled' => true,
            'zone' => $zone,
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $this->getConfigurationByChannels([$this->sharedStorage->get('channel')]),
            ],
        ]));
    }

    /**
     * @Given the store ships everywhere for :shippingMethodName
     * @Given the store ships everywhere with :shippingMethodName
     */
    public function theStoreShipsEverywhereWith(string $shippingMethodName): void
    {
        /** @var ZoneInterface $zone */
        foreach ($this->zoneRepository->findBy(['scope' => [CoreScope::SHIPPING, Scope::ALL]]) as $zone) {
            $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
                'name' => ucfirst($shippingMethodName),
                'code' => strtoupper($shippingMethodName) . '-' . $zone->getCode(),
                'enabled' => true,
                'zone' => $zone,
                'calculator' => [
                    'type' => DefaultCalculators::FLAT_RATE,
                    'configuration' => $this->getConfigurationByChannels([$this->sharedStorage->get('channel')]),
                ],
            ]));
        }
    }

    /**
     * @Given /^the store ships everywhere for free for (all channels)$/
     */
    public function theStoreShipsEverywhereForFreeForAllChannels(array $channels): void
    {
        foreach ($this->zoneRepository->findBy(['scope' => [CoreScope::SHIPPING, Scope::ALL]]) as $zone) {
            $configuration = $this->getConfigurationByChannels($channels);
            $shippingMethod = $this->shippingMethodExampleFactory->create([
                'name' => 'Free',
                'enabled' => true,
                'zone' => $zone,
                'calculator' => [
                    'type' => DefaultCalculators::FLAT_RATE,
                    'configuration' => $configuration,
                ],
                'channels' => $channels,
            ]);

            $this->saveShippingMethod($shippingMethod);
        }
    }

    /**
     * @Given the store (also )allows shipping with :name
     */
    public function theStoreAllowsShippingMethodWithName(string $name): void
    {
        $this->saveShippingMethod($this->shippingMethodExampleFactory->create(['name' => $name, 'enabled' => true]));
    }

    /**
     * @Given the store (also )allows shipping with :name identified by :code
     */
    public function theStoreAllowsShippingMethodWithNameAndCode(string $name, string $code): void
    {
        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $name,
            'zone' => $this->getShippingZone(),
            'enabled' => true,
            'code' => $code,
        ]));
    }

    /**
     * @Given the store (also )allows shipping with :name at position :position
     */
    public function theStoreAllowsShippingMethodWithNameAndPosition(string $name, int $position): void
    {
        $shippingMethod = $this->shippingMethodExampleFactory->create([
            'name' => $name,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
        ]);

        $shippingMethod->setPosition($position);

        $this->saveShippingMethod($shippingMethod);
    }

    /**
     * @Given /^the store(?:| also) allows shipping with "([^"]+)" at position (\d+) with ("[^"]+") fee$/
     */
    public function theStoreAllowsShippingMethodWithNameAndPositionAndFee(string $name, int $position, int $fee): void
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $shippingMethod = $this->shippingMethodExampleFactory->create([
            'name' => $name,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$channel],
        ]);

        $shippingMethod->setPosition($position);

        $this->saveShippingMethod($shippingMethod);
    }

    /**
     * @Given /^(this shipping method) is named "([^"]+)" in the ("[^"]+" locale)$/
     */
    public function thisShippingMethodIsNamedInLocale(
        ShippingMethodInterface $shippingMethod,
        string $name,
        string $locale,
    ): void {
        $translations = $shippingMethod->getTranslations();
        /** @var ShippingMethodTranslationInterface $translation */
        foreach ($translations as $translation) {
            if ($translation->getLocale() === $locale) {
                $translation->setName($name);

                return;
            }
        }
    }

    /**
     * @Given the store allows shipping with :firstName and :secondName
     * @Given the store allows shipping with :firstName, :secondName and :thirdName
     */
    public function theStoreAllowsShippingWithAnd(string ...$names): void
    {
        foreach ($names as $name) {
            $this->theStoreAllowsShippingMethodWithName($name);
        }
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee within the ("[^"]+" zone)$/
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee for the (rest of the world)$/
     */
    public function storeHasShippingMethodWithFeeAndZone(string $shippingMethodName, int $fee, ZoneInterface $zone): void
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $zone,
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee$/
     */
    public function storeHasShippingMethodWithFee(string $shippingMethodName, int $fee): void
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per shipment for ("[^"]+" channel) and ("[^"]+") for ("[^"]+" channel)$/
     */
    public function storeHasShippingMethodWithFeePerShipmentForChannels(
        string $shippingMethodName,
        int $firstFee,
        ChannelInterface $firstChannel,
        int $secondFee,
        ChannelInterface $secondChannel,
    ): void {
        $configuration = [];
        $configuration[$firstChannel->getCode()] = ['amount' => $firstFee];
        $configuration[$secondChannel->getCode()] = ['amount' => $secondFee];

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$firstChannel, $secondChannel],
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per shipment for ("[^"]+" channel)$/
     */
    public function storeHasShippingMethodWithFeePerShipmentForChannel(
        string $shippingMethodName,
        int $fee,
        ChannelInterface $channel,
    ): void {
        $configuration = [$channel->getCode() => ['amount' => $fee]];

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$channel],
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per unit for ("[^"]+" channel)$/
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per unit for ("[^"]+" channel) and ("[^"]+") for ("[^"]+" channel)$/
     */
    public function storeHasShippingMethodWithFeePerUnitForChannels(
        string $shippingMethodName,
        int $firstFee,
        ChannelInterface $firstChannel,
        ?int $secondFee = null,
        ?ChannelInterface $secondChannel = null,
    ): void {
        $configuration = [];
        $channels = [];

        $configuration[$firstChannel->getCode()] = ['amount' => $firstFee];
        $channels[] = $firstChannel;

        if (null !== $secondFee) {
            $configuration[$secondChannel->getCode()] = ['amount' => $secondFee];
            $channels[] = $secondChannel;
        }

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::PER_UNIT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => $channels,
        ]));
    }

    /**
     * @Given /^the store has disabled "([^"]+)" shipping method with ("[^"]+") fee$/
     */
    public function storeHasDisabledShippingMethodWithFee(string $shippingMethodName, int $fee): void
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => false,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
        ]));
    }

    /**
     * @Given /^the store has an archival "([^"]+)" shipping method with ("[^"]+") fee$/
     */
    public function theStoreHasArchivalShippingMethodWithFee(string $shippingMethodName, int $fee): void
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
            'archived_at' => new \DateTime(),
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per unit$/
     */
    public function theStoreHasShippingMethodWithFeePerUnit(string $shippingMethodName, int $fee): void
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::PER_UNIT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee not assigned to any channel$/
     */
    public function storeHasShippingMethodWithFeeNotAssignedToAnyChannel(string $shippingMethodName, int $fee): void
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [],
        ]));
    }

    /**
     * @Given /^(shipping method "[^"]+") belongs to ("[^"]+" tax category)$/
     */
    public function shippingMethodBelongsToTaxCategory(
        ShippingMethodInterface $shippingMethod,
        TaxCategoryInterface $taxCategory,
    ): void {
        $shippingMethod->setTaxCategory($taxCategory);
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given the shipping method :shippingMethod is enabled
     */
    public function theShippingMethodIsEnabled(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethod->enable();
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given the shipping method :shippingMethod is disabled
     */
    public function theShippingMethodIsDisabled(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethod->disable();
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^(this shipping method) requires at least one unit matches to ("([^"]+)" shipping category)$/
     */
    public function thisShippingMethodRequiresAtLeastOneUnitMatchToShippingCategory(
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory,
    ): void {
        $shippingMethod->setCategory($shippingCategory);
        $shippingMethod->setCategoryRequirement(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY);
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^(this shipping method) requires that all units match to ("([^"]+)" shipping category)$/
     */
    public function thisShippingMethodRequiresThatAllUnitsMatchToShippingCategory(
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory,
    ) {
        $shippingMethod->setCategory($shippingCategory);
        $shippingMethod->setCategoryRequirement(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL);
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^(this shipping method) requires that no units match to ("([^"]+)" shipping category)$/
     */
    public function thisShippingMethodRequiresThatNoUnitsMatchToShippingCategory(
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory,
    ): void {
        $shippingMethod->setCategory($shippingCategory);
        $shippingMethod->setCategoryRequirement(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE);
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^the (shipping method "[^"]+") is archival$/
     */
    public function theShippingMethodIsArchival(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethod->setArchivedAt(new \DateTime());
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^the shipping fee for ("[^"]+" shipping method) has been changed to ("[^"]+")$/
     */
    public function theShippingFeeForShippingMethodHasBeenChangedTo(ShippingMethodInterface $shippingMethod, $fee): void
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $shippingMethod->setConfiguration($configuration);

        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^(this shipping method) is only available for orders over or equal to ("[^"]+")$/
     */
    public function thisShippingMethodIsOnlyAvailableForOrdersOverOrEqualTo(
        ShippingMethodInterface $shippingMethod,
        int $amount,
    ): void {
        $rule = $this->createShippingMethodRule(
            OrderTotalGreaterThanOrEqualRuleChecker::TYPE,
            $this->getConfigurationByChannels([$this->sharedStorage->get('channel')], $amount),
        );

        $this->addRuleToShippingMethod($rule, $shippingMethod);
    }

    /**
     * @Given /^(this shipping method) is only available for orders under or equal to ("[^"]+")$/
     */
    public function thisShippingMethodIsOnlyAvailableForOrdersUnderOrEqualTo(
        ShippingMethodInterface $shippingMethod,
        int $amount,
    ): void {
        $rule = $this->createShippingMethodRule(
            OrderTotalLessThanOrEqualRuleChecker::TYPE,
            $this->getConfigurationByChannels([$this->sharedStorage->get('channel')], $amount),
        );

        $this->addRuleToShippingMethod($rule, $shippingMethod);
    }

    /**
     * @Given /^(this shipping method) is only available for orders with a total weight greater or equal to (\d+\.\d+)$/
     */
    public function thisShippingMethodIsOnlyAvailableForOrdersWithATotalWeightGreaterOrEqualTo(
        ShippingMethodInterface $shippingMethod,
        float $weight,
    ): void {
        $rule = $this->createShippingMethodRule(TotalWeightGreaterThanOrEqualRuleChecker::TYPE, [
            'weight' => $weight,
        ]);

        $this->addRuleToShippingMethod($rule, $shippingMethod);
    }

    /**
     * @Given /^(this shipping method) is only available for orders with a total weight less or equal to (\d+\.\d+)$/
     */
    public function thisShippingMethodIsOnlyAvailableForOrdersWithATotalWeightLessOrEqualTo(
        ShippingMethodInterface $shippingMethod,
        float $weight,
    ): void {
        $rule = $this->createShippingMethodRule(TotalWeightLessThanOrEqualRuleChecker::TYPE, [
            'weight' => $weight,
        ]);

        $this->addRuleToShippingMethod($rule, $shippingMethod);
    }

    /**
     * @Given /^(this shipping method) has been disabled$/
     * @Given /^(this shipping method) has been disabled for ("[^"]+" channel)$/
     */
    public function thisShippingMethodHasBeenDisabled(ShippingMethodInterface $shippingMethod, ?ChannelInterface $channel = null): void
    {
        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $shippingMethod->getCode()]);

        if (null === $channel) {
            $shippingMethod->disable();
        } else {
            $shippingMethod->removeChannel($channel);
        }

        $this->shippingMethodManager->flush();
    }

    private function getConfigurationByChannels(array $channels, int $amount = 0): array
    {
        $configuration = [];

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $configuration[$channel->getCode()] = ['amount' => $amount];
        }

        return $configuration;
    }

    private function saveShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->shippingMethodRepository->add($shippingMethod);
        $this->sharedStorage->set('shipping_method', $shippingMethod);
    }

    private function getShippingZone(): ZoneInterface
    {
        if ($this->sharedStorage->has('shipping_zone')) {
            return $this->sharedStorage->get('shipping_zone');
        }

        return $this->sharedStorage->get('zone');
    }

    private function addRuleToShippingMethod(ShippingMethodRuleInterface $rule, ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethod->addRule($rule);
        $this->shippingMethodManager->flush();
    }

    private function createShippingMethodRule(string $type, array $configuration): ShippingMethodRuleInterface
    {
        /** @var ShippingMethodRuleInterface $rule */
        $rule = $this->shippingMethodRuleFactory->createNew();
        $rule->setType($type);
        $rule->setConfiguration($configuration);

        return $rule;
    }
}
