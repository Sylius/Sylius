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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\ProductReviewTransitions;
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
        $catalogPromotionScope->setType(CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS);
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
        $catalogPromotionScope->setType(CatalogPromotionScopeInterface::TYPE_FOR_PRODUCTS);
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
        $catalogPromotionAction->setType(CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT);
        $catalogPromotionAction->setConfiguration(['amount' => $discount]);

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
                'type' => CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS,
                'configuration' => ['variants' => $variantCodes],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                'configuration' => ['amount' => $discount],
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
                'type' => CatalogPromotionScopeInterface::TYPE_FOR_TAXONS,
                'configuration' => ['taxons' => [$taxon->getCode()]],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
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
                'type' => CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
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
                'type' => CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
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
                'type' => CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
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
                'type' => CatalogPromotionScopeInterface::TYPE_FOR_PRODUCTS,
                'configuration' => ['products' => [$product->getCode()]],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion "([^"]+)" with priority ([^"]+) that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsACatalogPromotionWithPriorityThatReducesPriceByAndAppliesOnVarian(
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
                'type' => CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                'configuration' => ['amount' => $discount],
            ]],
            $priority
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
        int $priority = null
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
        ]);

        $this->entityManager->persist($catalogPromotion);
        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);

        return $catalogPromotion;
    }
}
