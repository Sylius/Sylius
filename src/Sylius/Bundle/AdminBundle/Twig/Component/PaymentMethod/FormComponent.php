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

namespace Sylius\Bundle\AdminBundle\Twig\Component\PaymentMethod;

use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'sylius_admin:payment_method:form', template: '@SyliusAdmin/payment_method/form.html.twig')]
final class FormComponent
{
    use DefaultActionTrait;
    use HookableComponentTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?PaymentMethod $paymentMethod = null;

    /** @param class-string $formClass */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $formClass,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->paymentMethod);
    }
}
