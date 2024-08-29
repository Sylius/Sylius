<?php

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Twig\Component\Checkout;

use Sylius\Component\Core\Model\Order;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ShippingStepFormComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'order')]
    public ?Order $order = null;

    /** @param class-string $formClass */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $formClass,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->order);
    }
}
