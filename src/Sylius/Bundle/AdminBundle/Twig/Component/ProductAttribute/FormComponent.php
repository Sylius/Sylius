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

namespace Sylius\Bundle\AdminBundle\Twig\Component\ProductAttribute;

use Sylius\Component\Product\Model\ProductAttribute;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
class FormComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'resource')]
    public ?ProductAttribute $resource = null;

    #[LiveProp(fieldName: 'type')]
    public ?string $type = null;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $productAttributeTypeClass,
    ) {
    }

    #[LiveAction]
    public function addCollectionItem(PropertyAccessorInterface $propertyAccessor, #[LiveArg] string $name): void
    {
        $propertyPath = $this->fieldNameToPropertyPath($name, $this->formName);
        $index = count($propertyAccessor->getValue($this->formValues, $propertyPath));
        $propertyAccessor->setValue($this->formValues, $propertyPath . "[$index]", []);
    }

    #[LiveAction]
    public function removeCollectionItem(PropertyAccessorInterface $propertyAccessor, #[LiveArg] string $name, #[LiveArg] string $index): void
    {
        $propertyPath = $this->fieldNameToPropertyPath($name, $this->formName);
        $data = $propertyAccessor->getValue($this->formValues, $propertyPath);
        unset($data[$index]);
        $propertyAccessor->setValue($this->formValues, $propertyPath, $data);
    }

    protected function instantiateForm(): FormInterface
    {
        $this->resource->setType($this->type);

        return $this->formFactory->create($this->productAttributeTypeClass, $this->resource);
    }
}
