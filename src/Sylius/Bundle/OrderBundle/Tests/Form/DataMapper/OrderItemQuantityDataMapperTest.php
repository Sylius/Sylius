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

namespace Tests\Sylius\Bundle\OrderBundle\Form\DataMapper;

use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Form\DataMapper\OrderItemQuantityDataMapper;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormInterface;

final class OrderItemQuantityDataMapperTest extends TestCase
{
    /** @var OrderItemQuantityModifierInterface&MockObject */
    private OrderItemQuantityModifierInterface $orderItemQuantityModifier;

    /** @var DataMapperInterface&MockObject */
    private DataMapperInterface $propertyPathDataMapper;

    private OrderItemQuantityDataMapper $orderItemQuantityDataMapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemQuantityModifier = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->propertyPathDataMapper = $this->createMock(DataMapperInterface::class);
        $this->orderItemQuantityDataMapper = new OrderItemQuantityDataMapper($this->orderItemQuantityModifier, $this->propertyPathDataMapper);
    }

    public function testImplementsADataMapperInterface(): void
    {
        self::assertInstanceOf(DataMapperInterface::class, $this->orderItemQuantityDataMapper);
    }

    public function testUsesAPropertyPathDataMapperWhileMappingDataToForms(): void
    {
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var OrderItemInterface&MockObject $orderItem */
        $orderItem = $this->createMock(OrderItemInterface::class);

        $forms = new ArrayIterator([$form]);

        $this->propertyPathDataMapper->expects(self::once())
            ->method('mapDataToForms')
            ->with($orderItem, $forms);

        $this->orderItemQuantityDataMapper->mapDataToForms($orderItem, $forms);
    }
}
