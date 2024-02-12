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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Product;

use Sylius\Bundle\AdminBundle\TwigComponent\HookableComponentTrait;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(name: 'SyliusAdmin.Product.Form', template: '@SyliusAdmin/Product/_form.html.twig')]
final class FormComponent
{
    use DefaultActionTrait;
    use HookableComponentTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?Product $resource = null;

    /** @param class-string $formClass */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $formClass,
        private readonly LocaleContextInterface $localeContext,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->resource);
    }

    #[ExposeInTemplate]
    public function getMappedProductAttributes(): array
    {
        $mappedAttributes = [];

        $attributes = $this->getForm()->createView()->children['attributes'];

        foreach ($attributes->children as $attribute) {
            /** @var ProductAttributeValueInterface $productAttributeValue */
            $productAttributeValue = $attribute->vars['value'];

            $mappedAttributes[$productAttributeValue->getAttribute()->getCode()][$productAttributeValue->getLocaleCode()] = $attribute;
        }

        return $mappedAttributes;
    }

    #[LiveAction]
    public function applyToAll(#[LiveArg] $attributeCode, #[LiveArg] $localeCode): void
    {
        $matchingAttributes = array_filter(
            $this->formValues['attributes'],
            fn (array $value) => $value['attribute'] === $attributeCode && $value['localeCode'] === $localeCode
        );
        $currentValue = array_pop($matchingAttributes)['value'];

        $this->formValues['attributes'] = array_map(
            fn (array $value) => $value['attribute'] === $attributeCode
                ? ['attribute' => $attributeCode, 'localeCode' => $value['localeCode'], 'value' => $currentValue]
                : $value,
            $this->formValues['attributes'],
        );
    }
}
