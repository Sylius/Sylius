<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Promotion\Action;

use Sylius\Order\Model\OrderInterface;
use Sylius\Order\Model\OrderItemInterface;
use Sylius\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Promotion\Action\PromotionActionInterface;
use Sylius\Promotion\Model\PromotionInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Repository\RepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class AddProductAction implements PromotionActionInterface
{
    /**
     * @var FactoryInterface
     */
    protected $itemFactory;

    /**
     * @var RepositoryInterface
     */
    protected $variantRepository;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    protected $orderItemQuantityModifier;

    /**
     * @param FactoryInterface $itemFactory
     * @param RepositoryInterface $variantRepository
     * @param OrderItemQuantityModifierInterface $orderItemQuantityModifier
     */
    public function __construct(
        FactoryInterface $itemFactory,
        RepositoryInterface $variantRepository,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->itemFactory = $itemFactory;
        $this->variantRepository = $variantRepository;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if ($subject instanceof OrderItemInterface) {
            return;
        }

        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $promotionItem = $this->createItem($configuration);

        foreach ($subject->getItems() as $item) {
            if ($item->equals($promotionItem)) {
                return;
            }
        }

        $promotionItem->setImmutable(true);

        $subject->addItem($promotionItem);
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if ($subject instanceof OrderItemInterface) {
            return;
        }

        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $promotionItem = $this->createItem($configuration);
        $promotionItem->setImmutable(true);

        foreach ($subject->getItems() as $item) {
            if ($item->equals($promotionItem)) {
                $subject->removeItem($item);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_add_product_configuration';
    }

    /**
     * @param array $configuration
     *
     * @return OrderItemInterface
     */
    protected function createItem(array $configuration)
    {
        $variant = $this->variantRepository->find($configuration['variant']);

        $promotionItem = $this->itemFactory->createNew();
        $promotionItem->setVariant($variant);
        $promotionItem->setUnitPrice((int) $configuration['price']);

        $this->orderItemQuantityModifier->modify($promotionItem, (int) $configuration['quantity']);

        return $promotionItem;
    }
}
