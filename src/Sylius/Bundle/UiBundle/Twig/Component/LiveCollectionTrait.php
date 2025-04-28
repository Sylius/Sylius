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

namespace Sylius\Bundle\UiBundle\Twig\Component;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

/** @see \Symfony\UX\LiveComponent\LiveCollectionTrait */
trait LiveCollectionTrait
{
    use ComponentWithFormTrait;

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
        $propertyAccessor->setValue(
            $this->formValues,
            sprintf('%s[%s]', $propertyPath, $index),
            null === $type ? [] : ['type' => $type],
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
        unset($data[$index]);
        $propertyAccessor->setValue($this->formValues, $propertyPath, $data);
    }

    protected function fieldNameToPropertyPath(string $collectionFieldName, string $rootFormName): string
    {
        $propertyPath = $collectionFieldName;

        if (str_starts_with($collectionFieldName, $rootFormName)) {
            $propertyPath = substr_replace($collectionFieldName, '', 0, mb_strlen($rootFormName));
        }

        if (!str_starts_with($propertyPath, '[')) {
            $propertyPath = "[$propertyPath]";
        }

        return $propertyPath;
    }

    /** @param array<int|string, mixed> $data */
    protected function provideNewCollectionItemIndex(array $data): int
    {
        if ([] === $data) {
            return 0;
        }

        $keys = array_keys($data);
        $numericKeys = array_filter($keys, 'is_numeric');
        if ([] === $numericKeys) {
            return 0;
        }

        if (count($keys) === count($numericKeys)) {
            return max($keys) + 1;
        }

        return max($numericKeys) + 1;
    }
}
