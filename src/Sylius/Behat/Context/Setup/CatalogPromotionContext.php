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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Provider\ForProductsScopeVariantsProvider;
use Sylius\Bundle\CoreBundle\Provider\ForTaxonsScopeVariantsProvider;
use Sylius\Bundle\CoreBundle\Provider\ForVariantsScopeVariantsProvider;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionContext implements Context
{
    private ExampleFactoryInterface $catalogPromotionExampleFactory;

    private FactoryInterface $catalogPromotionScopeFactory;

    private FactoryInterface $catalogPromotionActionFactory;

    private EntityManagerInterface $entityManager;

    private ChannelRepositoryInterface $channelRepository;

    private StateMachineFactoryInterface $stateMachineFactory;

    private MessageBusInterface $eventBus;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ExampleFactoryInterface $catalogPromotionExampleFactory,
        FactoryInterface $catalogPromotionScopeFactory,
        FactoryInterface $catalogPromotionActionFactory,
        EntityManagerInterface $entityManager,
        ChannelRepositoryInterface $channelRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        MessageBusInterface $eventBus,
        SharedStorageInterface $sharedStorage
    ) {
        $this->catalogPromotionExampleFactory = $catalogPromotionExampleFactory;
        $this->catalogPromotionScopeFactory = $catalogPromotionScopeFactory;
        $this->catalogPromotionActionFactory = $catalogPromotionActionFactory;
        $this->entityManager = $entityManager;
        $this->channelRepository = $channelRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->eventBus = $eventBus;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given there is a catalog promotion with :code code and :name name
     */
    public function thereIsACatalogPromotionWithCodeAndName(string $code, string $name): void
    {
        $this->createCatalogPromotion($name, $code);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) is enabled$/
     */
    public function itIsEnabled(CatalogPromotionInterface $catalogPromotion): void
    {
        $catalogPromotion->setEnabled(true);

        $this->entityManager->flush();

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given /^(this catalog promotion) is disabled$/
     */
    public function thisCatalogPromotionIsDisabled(CatalogPromotionInterface $catalogPromotion): void
    {
        $catalogPromotion->setEnabled(false);

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));

        $this->entityManager->flush();
    }

    /**
     * @Given there are catalog promotions named :firstName and :secondName
     * @Given there is a catalog promotion named :name
     */
    public function thereAreCatalogPromotionsNamed(string ...$names): void
    {
        foreach ($names as $name) {
            $this->createCatalogPromotion($name);
        }

        $this->entityManager->flush();
    }

    /**
     * @Given the catalog promotion :catalogPromotion is available in :channel
     */
    public function theCatalogPromotionIsAvailableIn(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel
    ): void {
        $catalogPromotion->addChannel($channel);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) applies(?:| also) on ("[^"]+" variant)$/
     */
    public function itAppliesOnVariant(CatalogPromotionInterface $catalogPromotion, ProductVariantInterface $variant): void
    {
        /** @var CatalogPromotionScopeInterface $catalogPromotionScope */
        $catalogPromotionScope = $this->catalogPromotionScopeFactory->createNew();
        $catalogPromotionScope->setType(ForVariantsScopeVariantsProvider::TYPE);
        $catalogPromotionScope->setConfiguration(['variants' => [$variant->getCode()]]);

        $catalogPromotion->addScope($catalogPromotionScope);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) applies(?:| also) on ("[^"]+" product)$/
     */
    public function itAppliesOnProduct(CatalogPromotionInterface $catalogPromotion, ProductInterface $product): void
    {
        /** @var CatalogPromotionScopeInterface $catalogPromotionScope */
        $catalogPromotionScope = $this->catalogPromotionScopeFactory->createNew();
        $catalogPromotionScope->setType(ForProductsScopeVariantsProvider::TYPE);
        $catalogPromotionScope->setConfiguration(['products' => [$product->getCode()]]);

        $catalogPromotion->addScope($catalogPromotionScope);

        $this->entityManager->flush();

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given /^(it) reduces price by ("[^"]+")$/
     */
    public function itWillReducePrice(CatalogPromotionInterface $catalogPromotion, float $discount): void
    {
        /** @var CatalogPromotionActionInterface $catalogPromotionAction */
        $catalogPromotionAction = $this->catalogPromotionActionFactory->createNew();
        $catalogPromotionAction->setType(PercentageDiscountPriceCalculator::TYPE);
        $catalogPromotionAction->setConfiguration(['amount' => $discount]);

        $catalogPromotion->addAction($catalogPromotionAction);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) reduces(?:| also) price by fixed ("[^"]+") in the ("[^"]+" channel)$/
     */
    public function itReducesPriceByFixedInTheChannel(
        CatalogPromotionInterface $catalogPromotion,
        int $discount,
        ChannelInterface $channel
    ): void {
        /** @var CatalogPromotionActionInterface $catalogPromotionAction */
        $catalogPromotionAction = $this->catalogPromotionActionFactory->createNew();
        $catalogPromotionAction->setType(FixedDiscountPriceCalculator::TYPE);
        $catalogPromotionAction->setConfiguration([$channel->getCode() => ['amount' => $discount]]);

        $catalogPromotion->addAction($catalogPromotionAction);

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion "([^"]*)" that reduces price by ("[^"]+") and applies on ("[^"]+" variant) and ("[^"]+" variant)$/
     * @Given /^there is a catalog promotion "([^"]*)" that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsACatalogPromotionThatReducesPriceByAndAppliesOn(
        string $name,
        float $discount,
        ProductVariantInterface ...$variants
    ): void {
        $variantCodes = [];
        foreach ($variants as $variant) {
            $variantCodes[] = $variant->getCode();
        }

        $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForVariantsScopeVariantsProvider::TYPE,
                'configuration' => ['variants' => $variantCodes],
            ]],
            [[
                'type' => PercentageDiscountPriceCalculator::TYPE,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion "([^"]*)" that reduces price by fixed ("[^"]+") in the ("[^"]+" channel) and applies on ("[^"]+" variant)$/
     */
    public function thereIsACatalogPromotionThatReducesPriceByFixedInTheChannelAndAppliesOnVariant(
        string $name,
        int $discount,
        ChannelInterface $channel,
        ProductVariantInterface $variant
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForVariantsScopeVariantsProvider::TYPE,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => FixedDiscountPriceCalculator::TYPE,
                'configuration' => [$channel->getCode() => ['amount' => $discount]],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion "([^"]*)" that reduces price by fixed ("[^"]+") in the ("[^"]+" channel) and applies on ("[^"]+" product)$/
     */
    public function thereIsACatalogPromotionThatReducesPriceByFixedInTheChannelAndAppliesOnProduct(
        string $name,
        int $discount,
        ChannelInterface $channel,
        ProductInterface $product
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForProductsScopeVariantsProvider::TYPE,
                'configuration' => ['products' => [$product->getCode()]],
            ]],
            [[
                'type' => FixedDiscountPriceCalculator::TYPE,
                'configuration' => [$channel->getCode() => ['amount' => $discount]],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion "([^"]*)" that reduces price by fixed ("[^"]+") in the ("[^"]+" channel) and applies on ("[^"]+" taxon)$/
     */
    public function thereIsACatalogPromotionThatReducesPriceByFixedInTheChannelAndAppliesOnTaxon(
        string $name,
        int $discount,
        ChannelInterface $channel,
        TaxonInterface $taxon
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForTaxonsScopeVariantsProvider::TYPE,
                'configuration' => ['taxons' => [$taxon->getCode()]],
            ]],
            [[
                'type' => FixedDiscountPriceCalculator::TYPE,
                'configuration' => [$channel->getCode() => ['amount' => $discount]],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given catalog promotion :catalogPromotion has failed processing
     */
    public function catalogPromotionHasFailedProcessing(CatalogPromotionInterface $catalogPromotion): void
    {
        $stateMachine = $this->stateMachineFactory->get($catalogPromotion, CatalogPromotionTransitions::GRAPH);

        $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_PROCESS);
        $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_FAIL);

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given /^there is (?:a|another) catalog promotion "([^"]*)" that reduces price by ("[^"]+") and applies on ("[^"]+" taxon) and ("[^"]+" taxon)$/
     * @Given /^there is (?:a|another) catalog promotion "([^"]*)" that reduces price by ("[^"]+") and applies on ("[^"]+" taxon)$/
     */
    public function thereIsACatalogPromotionThatReducesPriceByAndAppliesOnTaxon(
        string $name,
        float $discount,
        TaxonInterface $taxon
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForTaxonsScopeVariantsProvider::TYPE,
                'configuration' => ['taxons' => [$taxon->getCode()]],
            ]],
            [[
                'type' => PercentageDiscountPriceCalculator::TYPE,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion "([^"]*)" available in ("[^"]+" channel) that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsACatalogPromotionAvailableInChannelThatReducesPriceByAndAppliesOnVariant(
        string $name,
        ChannelInterface $channel,
        float $discount,
        ProductVariantInterface $variant
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [$channel->getCode()],
            [[
                'type' => ForVariantsScopeVariantsProvider::TYPE,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => PercentageDiscountPriceCalculator::TYPE,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is another catalog promotion "([^"]*)" available in ("[^"]+" channel) and ("[^"]+" channel) that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsAnotherCatalogPromotionAvailableInChannelsThatReducesPriceByAndAppliesOnVariant(
        string $name,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        float $discount,
        ProductVariantInterface $variant
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [
                $firstChannel->getCode(),
                $secondChannel->getCode()
            ],
            [[
                'type' => ForVariantsScopeVariantsProvider::TYPE,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => PercentageDiscountPriceCalculator::TYPE,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is another catalog promotion "([^"]*)" available in ("[^"]+" channel) that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsAnotherCatalogPromotionAvailableInChannelThatReducesPriceByAndAppliesOnVariant(
        string $name,
        ChannelInterface $channel,
        float $discount,
        ProductVariantInterface $variant
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [$channel->getCode()],
            [[
                'type' => ForVariantsScopeVariantsProvider::TYPE,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => PercentageDiscountPriceCalculator::TYPE,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is(?: a| another) catalog promotion "([^"]*)" that reduces price by ("[^"]+") and applies on ("[^"]+" product)$/
     */
    public function thereIsACatalogPromotionThatReducesPriceByAndAppliesOnProduct(
        string $name,
        float $discount,
        ProductInterface $product
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForProductsScopeVariantsProvider::TYPE,
                'configuration' => ['products' => [$product->getCode()]],
            ]],
            [[
                'type' => PercentageDiscountPriceCalculator::TYPE,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion "([^"]+)" with priority ([^"]+)$/
     */
    public function thereIsACatalogPromotionWithPriority(
        string $name,
        int $priority
    ): void {
        $catalogPromotion = $this->createCatalogPromotion(name: $name, priority: $priority);

        $this->entityManager->flush();

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given /^there is a catalog promotion "([^"]+)" with priority ([^"]+) that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsACatalogPromotionWithPriorityThatReducesPriceByAndAppliesOnVariant(
        string $name,
        int $priority,
        float $discount,
        ProductVariantInterface $variant
    ): void {
        $catalogPromotion = $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForVariantsScopeVariantsProvider::TYPE,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => PercentageDiscountPriceCalculator::TYPE,
                'configuration' => ['amount' => $discount],
            ]],
            $priority
        );

        $this->entityManager->flush();

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given /^there is (?:an|another) exclusive catalog promotion "([^"]+)" with priority ([^"]+) that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsAnExclusiveCatalogPromotionWithPriorityThatReducesPriceByAndAppliesOnVariant(
        string $name,
        int $priority,
        float $discount,
        ProductVariantInterface $variant
    ): void {
        $catalogPromotion = $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForVariantsScopeVariantsProvider::TYPE,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => PercentageDiscountPriceCalculator::TYPE,
                'configuration' => ['amount' => $discount],
            ]],
            $priority,
            true
        );

        $this->entityManager->flush();

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given /^there is a catalog promotion "([^"]+)" with priority ([^"]+) that reduces price by fixed ("[^"]+") in the ("[^"]+" channel) and applies on ("[^"]+" product)$/
     */
    public function thereIsACatalogPromotionWithPriorityThatReducesPriceByFixedInTheChannelAndAppliesOnProduct(
        string $name,
        int $priority,
        int $discount,
        ChannelInterface $channel,
        ProductInterface $product
    ): void {
        $catalogPromotion = $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForProductsScopeVariantsProvider::TYPE,
                'configuration' => ['products' => [$product->getCode()]],
            ]],
            [[
                'type' => FixedDiscountPriceCalculator::TYPE,
                'configuration' => [$channel->getCode() => ['amount' => $discount]],
            ]],
            $priority
        );

        $this->entityManager->flush();

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given /^there is a catalog promotion "([^"]+)" with priority ([^"]+) that reduces price by fixed ("[^"]+") in the ("[^"]+" channel) and applies on ("[^"]+" taxon)$/
     */
    public function thereIsACatalogPromotionWithPriorityThatReducesPriceByFixedInTheChannelAndAppliesOnTaxon(
        string $name,
        int $priority,
        int $discount,
        ChannelInterface $channel,
        TaxonInterface $taxon
    ): void {
        $catalogPromotion = $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => ForTaxonsScopeVariantsProvider::TYPE,
                'configuration' => ['taxons' => [$taxon->getCode()]],
            ]],
            [[
                'type' => FixedDiscountPriceCalculator::TYPE,
                'configuration' => [$channel->getCode() => ['amount' => $discount]],
            ]],
            $priority,
        );

        $this->entityManager->flush();

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @When the :catalogPromotion catalog promotion is no longer available
     */
    public function theAdministratorMakesThisCatalogPromotionUnavailableInTheChannel(
        CatalogPromotionInterface $catalogPromotion
    ): void {
        foreach ($this->channelRepository->findAll() as $channel) {
            $catalogPromotion->removeChannel($channel);
        }
        $this->entityManager->persist($catalogPromotion);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given the catalog promotion :catalogPromotion operates between :startDate and :endDate
     */
    public function theCatalogPromotionOperatesBetweenDates(
        CatalogPromotionInterface $catalogPromotion,
        string $startDate,
        string $endDate
    ): void {
        $catalogPromotion->setStartDate(new \DateTime($startDate));
        $catalogPromotion->setEndDate(new \DateTime($endDate));

        $this->entityManager->flush();
        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given the catalog promotion :catalogPromotion starts at :startDate
     */
    public function theCatalogPromotionStartsAt(CatalogPromotionInterface $catalogPromotion, string $startDate): void
    {
        $catalogPromotion->setStartDate(new \DateTime($startDate));

        $this->entityManager->flush();
        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given the end date of catalog promotion :catalogPromotion was changed to :endDate
     */
    public function theEndDateOfCatalogPromotionWasChangedTo(
        CatalogPromotionInterface $catalogPromotion,
        string $endDate
    ): void {
        $catalogPromotion->setEndDate(new \DateTime($endDate));

        $this->entityManager->flush();
        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given /^(its) priority is ([^"]+)$/
     */
    public function theCatalogPromotionPriorityIs(CatalogPromotionInterface $catalogPromotion, int $priority): void
    {
        $catalogPromotion->setPriority($priority);

        $this->entityManager->flush();
        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    /**
     * @Given the catalog promotion :catalogPromotion is currently being processed
     */
    public function theCatalogPromotionIsCurrentlyBeingProcessed(CatalogPromotionInterface $catalogPromotion): void
    {
        $stateMachine = $this->stateMachineFactory->get($catalogPromotion, CatalogPromotionTransitions::GRAPH);
        $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_PROCESS);

        $this->entityManager->flush();
    }

    private function createCatalogPromotion(
        string $name,
        ?string $code = null,
        array $channels = [],
        array $scopes = [],
        array $actions = [],
        int $priority = null,
        bool $exclusive = false
    ): CatalogPromotionInterface {
        if (empty($channels) && $this->sharedStorage->has('channel')) {
            $channels = [$this->sharedStorage->get('channel')];
        }

        $code = $code ?? StringInflector::nameToCode($name);

        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionExampleFactory->create([
            'name' => $name,
            'code' => $code,
            'channels' => $channels,
            'actions' => $actions,
            'scopes' => $scopes,
            'description' => $name . ' description',
            'priority' => $priority,
            'exclusive' => $exclusive,
        ]);

        $this->entityManager->persist($catalogPromotion);
        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);

        return $catalogPromotion;
    }
}
