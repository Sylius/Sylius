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

namespace Sylius\Bundle\OrderBundle\Form\DataMapper;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Form\DataMapperInterface;

/**
 * @internal
 */
class OrderItemQuantityDataMapper implements DataMapperInterface
{
    public function __construct(
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        private DataMapperInterface $propertyPathDataMapper,
    ) {
    }

    public function mapDataToForms($viewData, $forms): void
    {
        $this->propertyPathDataMapper->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData($forms, &$viewData): void
    {
        $formsOtherThanQuantity = [];
        foreach ($forms as $form) {
            if ('quantity' === $form->getName()) {
                $targetQuantity = $form->getData();
                $this->orderItemQuantityModifier->modify($viewData, (int) $targetQuantity);

                continue;
            }

            $formsOtherThanQuantity[] = $form;
        }

        if (!empty($formsOtherThanQuantity)) {
            $this->propertyPathDataMapper->mapFormsToData(new ArrayCollection($formsOtherThanQuantity), $viewData);
        }
    }
}
