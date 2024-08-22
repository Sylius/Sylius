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
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Order\SyliusCartEvents;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class FormComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ComponentToolsTrait;
    use ComponentWithFormTrait;

    public const SYLIUS_SHOP_CART_CHANGED = 'sylius:shop:cart_changed';

    public const SYLIUS_SHOP_CART_CLEARED = 'sylius:shop:cart_cleared';

    #[LiveProp(fieldName: 'cart')]
    public ?Order $cart = null;

    public bool $shouldSaveCart = true;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $formClass,
        private readonly ObjectManager $manager,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->cart);
    }

    #[PreReRender(priority: -100)]
    public function saveCart(): void
    {
        if ($this->shouldSaveCart) {
            $form = $this->getForm();
            if ($form->isValid()) {
                $this->eventDispatcher->dispatch(new GenericEvent($form->getData()), SyliusCartEvents::CART_CHANGE);
                $this->manager->flush();
                $this->emit(self::SYLIUS_SHOP_CART_CHANGED, ['cart' => $this->cart->getId()]);
            }
        }
    }

    #[LiveAction]
    public function removeItem(#[LiveArg] int $index): void
    {
        $data = $this->formValues['items'];
        unset($data[$index]);
        $this->formValues['items'] = array_values($data);

        $orderItem = $this->cart->getItems()->get($index);
        $this->eventDispatcher->dispatch(new GenericEvent($orderItem), SyliusCartEvents::CART_ITEM_REMOVE);

        $this->manager->flush();
        $this->manager->refresh($this->cart);

        $this->shouldSaveCart = false;
        $this->submitForm();
        $this->emit(self::SYLIUS_SHOP_CART_CHANGED, ['cart' => $this->cart->getId()]);
    }

    #[LiveAction]
    public function clearCart(): void
    {
        $this->formValues['items'] = [];
        $this->manager->remove($this->cart);
        $this->manager->flush();

        $this->shouldSaveCart = false;
        $this->submitForm();
        $this->emit(self::SYLIUS_SHOP_CART_CLEARED);
    }

    private function getDataModelValue(): string
    {
        return 'debounce(500)|*';
    }
}
