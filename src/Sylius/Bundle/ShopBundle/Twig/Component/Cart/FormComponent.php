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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Cart;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent]
class FormComponent
{
    use ComponentToolsTrait;

    /** @use ResourceFormComponentTrait<OrderInterface> */
    use ResourceFormComponentTrait;

    use TemplatePropTrait;

    public const SYLIUS_SHOP_CART_CHANGED = 'sylius:shop:cart_changed';

    public const SYLIUS_SHOP_CART_CLEARED = 'sylius:shop:cart_cleared';

    public bool $shouldSaveCart = true;

    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly ObjectManager $manager,
        protected readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->initialize($orderRepository, $formFactory, $resourceClass, $formClass);
    }

    public function hydrateResource(mixed $value): ?ResourceInterface
    {
        if (empty($value)) {
            return $this->createResource();
        }

        /** @var OrderInterface|null $order */
        $order = $this->repository->find($value);

        if (!$order instanceof OrderInterface ||
            $order->getCheckoutState() === OrderCheckoutStates::STATE_COMPLETED
        ) {
            return $this->createResource();
        }

        return $order;
    }

    #[PreReRender(priority: -100)]
    public function saveCart(): void
    {
        if ($this->shouldSaveCart && $this->resource?->getId() !== null) {
            $form = $this->getForm();
            if ($form->isValid()) {
                $this->eventDispatcher->dispatch(new GenericEvent($form->getData()), SyliusCartEvents::CART_CHANGE);
                $this->manager->flush();
                $this->emit(self::SYLIUS_SHOP_CART_CHANGED, ['cartId' => $this->resource->getId()]);
            }
        }
    }

    #[LiveAction]
    public function removeItem(#[LiveArg] int $index): void
    {
        if ($this->resource?->getId() === null) {
            return;
        }

        $data = $this->formValues['items'];
        unset($data[$index]);
        $this->formValues['items'] = array_values($data);

        $orderItem = $this->resource->getItems()->get($index);
        $this->eventDispatcher->dispatch(new GenericEvent($orderItem), SyliusCartEvents::CART_ITEM_REMOVE);

        $this->manager->persist($this->resource);
        $this->manager->flush();
        $this->manager->refresh($this->resource);

        $this->shouldSaveCart = false;
        $this->submitForm();
        $this->emit(self::SYLIUS_SHOP_CART_CHANGED, ['cartId' => $this->resource->getId()]);
    }

    #[LiveAction]
    public function clearCart(): void
    {
        if ($this->resource?->getId() === null) {
            return;
        }

        $this->formValues['items'] = [];
        $this->eventDispatcher->dispatch(new GenericEvent($this->resource), SyliusCartEvents::CART_CLEAR);
        $this->manager->remove($this->resource);
        $this->manager->flush();

        $this->resource = $this->createResource();
        $this->resetForm();
        $this->isValidated = false;
        $this->validatedFields = [];

        $this->shouldSaveCart = false;
        $this->submitForm();
        $this->emit(self::SYLIUS_SHOP_CART_CLEARED);
    }

    #[LiveAction]
    public function removeCoupon(): void
    {
        $this->formValues['promotionCoupon'] = '';

        $this->submitForm();
    }

    private function getDataModelValue(): string
    {
        return 'debounce(500)|*';
    }
}
