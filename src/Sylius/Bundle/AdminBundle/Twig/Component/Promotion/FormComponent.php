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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Promotion;

use Sylius\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\PromotionInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
class FormComponent
{
    use LiveCollectionTrait;

    private const RULES_PROPERTY_PATH = '[rules]';

    private const ACTIONS_PROPERTY_PATH = '[actions]';

    /** @use ResourceFormComponentTrait<PromotionInterface> */
    use ResourceFormComponentTrait {
        initialize as public __construct;
    }

    use TemplatePropTrait;

    /** @var array<string, list<array<string, mixed>>> */
    #[LiveProp]
    public array $deletedRules = [];

    /** @var array<string, list<array<string, mixed>>> */
    #[LiveProp]
    public array $deletedActions = [];

    #[LiveAction]
    public function addCollectionItem(
        PropertyAccessorInterface $propertyAccessor,
        #[LiveArg]
        string $name,
        #[LiveArg]
        ?string $type = null,
    ): void {
        $propertyPath = $this->fieldNameToPropertyPath($name, $this->formName);
        $data = $propertyAccessor->getValue($this->formValues, $propertyPath);

        if (!\is_array($data)) {
            $propertyAccessor->setValue($this->formValues, $propertyPath, []);
            $data = [];
        }

        $index = $this->provideNewCollectionItemIndex($data);
        $item = null !== $type ? $this->recallDeletedItem($propertyPath, $type) : null;

        $propertyAccessor->setValue(
            $this->formValues,
            sprintf('%s[%s]', $propertyPath, $index),
            $item ?? (null === $type ? [] : ['type' => $type]),
        );
    }

    #[LiveAction]
    public function removeCollectionItem(
        PropertyAccessorInterface $propertyAccessor,
        #[LiveArg]
        string $name,
        #[LiveArg]
        int|string $index,
    ): void {
        $propertyPath = $this->fieldNameToPropertyPath($name, $this->formName);
        $data = $propertyAccessor->getValue($this->formValues, $propertyPath);

        $removedItem = $data[$index] ?? null;
        if (\is_array($removedItem) && isset($removedItem['type']) && \is_string($removedItem['type'])) {
            $this->rememberDeletedItem($propertyPath, $removedItem);
        }

        unset($data[$index]);
        $propertyAccessor->setValue($this->formValues, $propertyPath, $data);
    }

    /** @param array<string, mixed> $item */
    private function rememberDeletedItem(string $propertyPath, array $item): void
    {
        match ($propertyPath) {
            self::RULES_PROPERTY_PATH => $this->deletedRules[$item['type']][] = $item,
            self::ACTIONS_PROPERTY_PATH => $this->deletedActions[$item['type']][] = $item,
            default => null,
        };
    }

    /** @return array<string, mixed>|null */
    private function recallDeletedItem(string $propertyPath, string $type): ?array
    {
        return match ($propertyPath) {
            self::RULES_PROPERTY_PATH => $this->popDeletedItem($this->deletedRules, $type),
            self::ACTIONS_PROPERTY_PATH => $this->popDeletedItem($this->deletedActions, $type),
            default => null,
        };
    }

    /**
     * @param array<string, list<array<string, mixed>>> $deletedItems
     *
     * @return array<string, mixed>|null
     */
    private function popDeletedItem(array &$deletedItems, string $type): ?array
    {
        if ([] === ($deletedItems[$type] ?? [])) {
            return null;
        }

        $item = array_pop($deletedItems[$type]);

        if ([] === $deletedItems[$type]) {
            unset($deletedItems[$type]);
        }

        return $item;
    }
}
