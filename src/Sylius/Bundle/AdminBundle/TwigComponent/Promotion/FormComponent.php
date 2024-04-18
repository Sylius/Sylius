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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Promotion;

use Sylius\Bundle\AdminBundle\TwigComponent\HookableComponentTrait;
use Sylius\Component\Core\Model\Promotion;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(name: 'sylius_admin:promotion:form', template: '@SyliusAdmin/promotion/form.html.twig')]
final class FormComponent
{
    use DefaultActionTrait;
    use HookableComponentTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'resource')]
    public ?Promotion $resource = null;

    /**
     * @param class-string $formClass
     * @param array<string, string> $ruleTypes
     * @param array<string, string> $actionTypes
     */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $formClass,
        private readonly array $ruleTypes,
        private readonly array $actionTypes,
    ) {
    }

    #[LiveAction]
    public function addCollectionItem(PropertyAccessorInterface $propertyAccessor, #[LiveArg] string $name): void
    {
        $propertyPath = $this->fieldNameToPropertyPath($name, $this->formName);
        $data = $propertyAccessor->getValue($this->formValues, $propertyPath);

        if (!\is_array($data)) {
            $propertyAccessor->setValue($this->formValues, $propertyPath, []);
            $data = [];
        }

        $propertyAccessor->setValue(
            $this->formValues,
            sprintf('%s[%d]', $propertyPath, $this->resolveItemIndex($data)),
            ['type' => $this->provideItemType($name)],
        );
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->resource);
    }

    private function provideItemType(string $name): string
    {
        if (str_contains($name, 'rules')) {
            return array_key_first($this->ruleTypes);
        }

        if (str_contains($name, 'actions')) {
            return array_key_first($this->actionTypes);
        }

        return '';
    }

    /** @param array<array-key, array<string, mixed>> $data */
    private function resolveItemIndex(array $data): int
    {
        return [] !== $data ? max(array_keys($data)) + 1 : 0;
    }
}
