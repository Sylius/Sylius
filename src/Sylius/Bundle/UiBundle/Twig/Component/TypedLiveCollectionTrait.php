<?php

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Twig\Component;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

trait TypedLiveCollectionTrait
{
    use LiveCollectionTrait;

    #[LiveAction]
    public function addCollectionItem(
        PropertyAccessorInterface $propertyAccessor,
        #[LiveArg] string $name,
        #[LiveArg] string $type,
    ): void {
        $propertyPath = $this->fieldNameToPropertyPath($name, $this->formName);
        $data = $propertyAccessor->getValue($this->formValues, $propertyPath);

        if (!\is_array($data)) {
            $propertyAccessor->setValue($this->formValues, $propertyPath, []);
            $data = [];
        }

        $propertyAccessor->setValue(
            $this->formValues,
            sprintf('%s[%s]', $propertyPath, $this->resolveItemIndex($data)),
            ['type' => $type],
        );
    }

    /** @param array<array-key, array<string, mixed>> $data */
    private function resolveItemIndex(array $data): int
    {
        return [] !== $data ? max(array_keys($data)) + 1 : 0;
    }
}
