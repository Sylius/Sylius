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
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(name: 'SyliusAdmin.Product.Form', template: '@SyliusAdmin/Product/_form.html.twig')]
class FormComponent
{
    public const ATTRIBUTE_REMOVED_EVENT = 'sylius_admin:product:form:attributed_deleted';

    public const AUTOCOMPLETE_CLEAR_REQUESTED_EVENT = 'sylius_admin.product_attribute_autocomplete.clear_requested';

    use ComponentToolsTrait;
    use DefaultActionTrait;
    use HookableComponentTrait;
    use LiveCollectionTrait;

    #[LiveProp(hydrateWith: 'hydrateFormData', dehydrateWith: 'dehydrateFormData', fieldName: 'formData')]
    public ?Product $resource = null;

    /**
     * @var array<string>
     */
    #[LiveProp(writable: true, hydrateWith: 'hydrateAttributesToBeAdded', dehydrateWith: 'dehydrateAttributesToBeAdded')]
    public array $attributesToBeAdded = [];

    /**
     * @param class-string $formClass
     * @param RepositoryInterface<ProductAttributeInterface> $productAttributeRepository
     */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $formClass,
        private readonly SlugGeneratorInterface $slugGenerator,
        private readonly RepositoryInterface $productAttributeRepository,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->resource);
    }

    /**
     * @return array<string, array<string, FormView>>
     */
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
    public function applyToAll(#[LiveArg] string $attributeCode, #[LiveArg] string $localeCode): void
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

    #[LiveAction]
    public function removeAttribute(#[LiveArg] string $attributeCode): void
    {
        $this->formValues['attributes'] = array_filter(
            $this->formValues['attributes'],
            fn (array $value) => $value['attribute'] !== $attributeCode
        );
        $this->dispatchBrowserEvent(self::ATTRIBUTE_REMOVED_EVENT, ['attributeCode' => $attributeCode]);
    }

    #[LiveAction]
    public function addAttributes(): void
    {
        foreach ($this->attributesToBeAdded as $attributeCode) {
            $productAttribute = $this->productAttributeRepository->findOneBy(['code' => $attributeCode]);

            if (!$productAttribute->isTranslatable()) {
                $this->formValues['attributes'][] = [
                    'attribute' => $attributeCode,
                    'localeCode' => null,
                    'value' => '',
                ];

                continue;
            }

            foreach ($this->formValues['translations'] as $localesCode => $translation) {
                $this->formValues['attributes'][] = [
                    'attribute' => $attributeCode,
                    'localeCode' => $localesCode,
                    'value' => '',
                ];
            }
        }

        $this->dispatchBrowserEvent(self::AUTOCOMPLETE_CLEAR_REQUESTED_EVENT);
    }

    #[LiveAction]
    public function generateProductSlug(#[LiveArg] string $localeCode): void
    {
        $this->formValues['translations'][$localeCode]['slug'] = $this->slugGenerator->generate($this->formValues['translations'][$localeCode]['name']);
    }

    /**
     * @return array<string>
     */
    public function hydrateAttributesToBeAdded(string $value): array
    {
        if ('' === $value) {
            return [];
        }

        return explode(',', $value);
    }

    /**
     * @param array<string> $value
     */
    public function dehydrateAttributesToBeAdded(array $value): string
    {
        return implode(',', $value);
    }

    public function hydrateFormData(?string $value): Product
    {
        return unserialize($value);
    }

    public function dehydrateFormData(Product $value): string
    {
        return serialize($value);
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }
}
