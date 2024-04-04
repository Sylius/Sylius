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

namespace Sylius\Bundle\AdminBundle\TwigComponent\ShippingMethod;

use Sylius\Bundle\AdminBundle\TwigComponent\HookableComponentTrait;
use Sylius\Component\Core\Model\ShippingMethod;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(name: 'SyliusAdmin.ShippingMethod.Form', template: '@SyliusAdmin/ShippingMethod/form.html.twig')]
final class FormComponent
{
    use DefaultActionTrait;
    use HookableComponentTrait;
    use LiveCollectionTrait;

    #[LiveProp(dehydrateWith: 'dehydrateResource', fieldName: 'resource')]
    public ?ShippingMethod $resource;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $shippingMethodTypeClass,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->shippingMethodTypeClass, $this->resource);
    }

    public function dehydrateResource(): ?int
    {
        return $this->resource?->getId();
    }

    /**
     * @return array<string, mixed>
     */
    #[ExposeInTemplate(name: 'hookable_data')]
    public function getHookableData(): array
    {
        return [
            'parent_main_hook' => $this->parentMainHook,
            'parent_fallback_hook' => $this->parentFallbackHook,
            'resource' => $this->resource,
            'form' => $this->formView,
        ];
    }
}
