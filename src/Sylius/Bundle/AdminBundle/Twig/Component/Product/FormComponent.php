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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Product;

use Sylius\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class FormComponent
{
    public const ATTRIBUTE_REMOVED_EVENT = 'sylius_admin:product:form:attributed_deleted';

    public const AUTOCOMPLETE_CLEAR_REQUESTED_EVENT = 'sylius_admin.product_attribute_autocomplete.clear_requested';

    use ComponentToolsTrait;
    use LiveCollectionTrait;

    /** @use ResourceFormComponentTrait<ProductInterface> */
    use ResourceFormComponentTrait;

    /** @var array<string> */
    #[LiveProp(writable: true, hydrateWith: 'hydrateAttributesToBeAdded', dehydrateWith: 'dehydrateAttributesToBeAdded')]
    #[ExposeInTemplate(name: 'attributes_to_be_added')]
    public array $attributesToBeAdded = [];

    #[LiveProp(writable: false)]
    public bool $isSimple = false;

    /**
     * @param RepositoryInterface<ProductInterface> $productRepository
     * @param RepositoryInterface<ProductAttributeInterface> $productAttributeRepository
     * @param ProductFactoryInterface<ProductInterface> $productFactory
     */
    public function __construct(
        RepositoryInterface $productRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        private readonly SlugGeneratorInterface $slugGenerator,
        private readonly RepositoryInterface $productAttributeRepository,
        private readonly ProductFactoryInterface $productFactory,
    ) {
        $this->initialize($productRepository, $formFactory, $resourceClass, $formClass);
    }

    /**
     * @return array<string, array<string, FormView>>
     */
    #[ExposeInTemplate(name: 'mapped_product_attributes')]
    public function getMappedProductAttributes(): array
    {
        $mappedAttributes = [];

        $attributes = $this->getFormView()->children['attributes'];

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
            fn (array $value) => $value['attribute'] === $attributeCode && $value['localeCode'] === $localeCode,
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
            fn (array $value) => $value['attribute'] !== $attributeCode,
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

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->resource);
    }

    /** @return ProductInterface */
    protected function createResource(): ResourceInterface
    {
        return $this->isSimple ? $this->productFactory->createWithVariant() : $this->productFactory->createNew();
    }

    protected function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
