<?php

declare(strict_types=1);

namespace App\Service;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface as SyliusOrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderItemReorderer implements OrderItemReordererInterface
{
    private CartContextInterface $cartContext;
    private FactoryInterface $orderItemFactory;
    private OrderModifierInterface $orderModifier;
    private OrderItemQuantityModifierInterface $orderItemQuantityModifier;
    private AvailabilityCheckerInterface $availabilityChecker;
    private OrderProcessorInterface $orderProcessor;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CartContextInterface $cartContext,
        FactoryInterface $orderItemFactory,
        OrderModifierInterface $orderModifier,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        AvailabilityCheckerInterface $availabilityChecker,
        OrderProcessorInterface $orderProcessor,
        EntityManagerInterface $entityManager
    ) {
        $this->cartContext = $cartContext;
        $this->orderItemFactory = $orderItemFactory;
        $this->orderModifier = $orderModifier;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->availabilityChecker = $availabilityChecker;
        $this->orderProcessor = $orderProcessor;
        $this->entityManager = $entityManager;
    }

    public function reorder(SyliusOrderItemInterface $originalOrderItem): void
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $originalOrderItem->getVariant();
        Assert::notNull($variant, 'Original order item must have a variant.');
        Assert::notNull($variant->getProduct(), 'Variant must belong to a product.');

        if (!$variant->getProduct()->isEnabled() || !$variant->isEnabled()) {
            throw new \InvalidArgumentException('sylius.product_variant.not_available');
        }

        // Проверяем доступность именно добавляемого количества
        if (!$this->availabilityChecker->isStockSufficient($variant, $originalOrderItem->getQuantity())) {
            throw new \InvalidArgumentException('sylius.product_variant.out_of_stock');
        }

        /** @var OrderInterface $cart */
        $cart = $this->cartContext->getCart();

        $itemModified = false;
        foreach ($cart->getItems() as $cartItem) {
            /** @var SyliusOrderItemInterface $cartItem */
            if ($cartItem->getVariant() === $variant) {
                $newQuantity = $cartItem->getQuantity() + $originalOrderItem->getQuantity();

                // Проверяем, достаточно ли общего количества на складе, ЕСЛИ товар уже есть в корзине.
                // Важно: AvailabilityChecker проверяет, можно ли *добавить* указанное количество.
                // Если товар уже был в корзине, то для $originalOrderItem->getQuantity() проверка уже пройдена.
                // Если вы хотите убедиться, что общее новое количество не превышает общий сток,
                // то нужно проверить $newQuantity относительно общего стока варианта.
                // Однако, isStockSufficient($variant, $originalOrderItem->getQuantity()) уже гарантирует,
                // что мы можем добавить $originalOrderItem->getQuantity() к тому, что уже на руках у клиента.

                $this->orderItemQuantityModifier->modify($cartItem, $newQuantity);
                $itemModified = true;
                break;
            }
        }

        if (!$itemModified) {
            /** @var SyliusOrderItemInterface $newOrderItem */
            $newOrderItem = $this->orderItemFactory->createNew();
            $newOrderItem->setVariant($variant);
            // $newOrderItem->setUnitPrice($variant->getChannelPricingForChannel($cart->getChannel())->getPrice());
            // Установка цены должна происходить автоматически через OrderProcessor (OrderItemPriceUpdater)

            $this->orderItemQuantityModifier->modify($newOrderItem, $originalOrderItem->getQuantity());
            $this->orderModifier->addToOrder($cart, $newOrderItem);
        }

        // 1. Обработка заказа (пересчет сумм, применение скидок и т.д.)
        $this->orderProcessor->process($cart);

        // 2. Сохранение изменений в базе данных
        // Обычно достаточно persist для самой корзины, если связи (cascade persist) настроены правильно
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }
}