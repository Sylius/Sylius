<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\OrderBundle\Form\DataMapper;

use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Form\DataMapperInterface;

/**
 * @internal
 */
class OrderItemQuantityDataMapper implements DataMapperInterface
{
    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var DataMapperInterface */
    private $propertyPathDataMapper;

    public function __construct(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        DataMapperInterface $propertyPathDataMapper
    ) {
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->propertyPathDataMapper = $propertyPathDataMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms): void
    {
        $this->propertyPathDataMapper->mapDataToForms($data, $forms);
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data): void
    {
        $formsOtherThanQuantity = [];
        foreach ($forms as $form) {
            if ('quantity' === $form->getName()) {
                $targetQuantity = $form->getData();
                $this->orderItemQuantityModifier->modify($data, (int) $targetQuantity);

                continue;
            }

            $formsOtherThanQuantity[] = $form;
        }

        if (!empty($formsOtherThanQuantity)) {
            $this->propertyPathDataMapper->mapFormsToData($formsOtherThanQuantity, $data);
        }
    }
}
