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

namespace Tests\Sylius\Bundle\ApiBundle\Validator;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\DenormalizationViolationFactoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Validator\DenormalizationViolationFactory;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

#[CoversClass(DenormalizationViolationFactory::class)]
final class DenormalizationViolationFactoryTest extends TestCase
{
    private DenormalizationViolationFactoryInterface&MockObject $decoratedFactory;

    private DenormalizationViolationFactory $denormalizationViolationFactory;

    private NotNormalizableValueException $exception;

    protected function setUp(): void
    {
        parent::setUp();

        if (!interface_exists(DenormalizationViolationFactoryInterface::class)) {
            self::markTestSkipped('The denormalization violation factory is available since API Platform 5.');
        }

        $this->decoratedFactory = $this->createMock(DenormalizationViolationFactoryInterface::class);
        $this->denormalizationViolationFactory = new DenormalizationViolationFactory($this->decoratedFactory);
        $this->exception = NotNormalizableValueException::createForUnexpectedDataType('message', null, ['string'], 'code');
    }

    public function testRemovesValidationGroupsThatAreNotAnArray(): void
    {
        $operation = new Post(validationContext: ['groups' => 'sylius.validation_groups_generator', 'foo' => 'bar']);

        $this->decoratedFactory
            ->expects(self::once())
            ->method('handle')
            ->with($this->exception, self::callback(
                static fn (Operation $operation): bool => ['foo' => 'bar'] === $operation->getValidationContext(),
            ))
        ;

        $this->denormalizationViolationFactory->handle($this->exception, $operation);
    }

    public function testPassesOperationWithArrayValidationGroupsUnchanged(): void
    {
        $operation = new Post(validationContext: ['groups' => ['sylius']]);

        $this->decoratedFactory->expects(self::once())->method('handle')->with($this->exception, $operation);

        $this->denormalizationViolationFactory->handle($this->exception, $operation);
    }

    public function testPassesOperationWithoutValidationContextUnchanged(): void
    {
        $operation = new Post();

        $this->decoratedFactory->expects(self::once())->method('handle')->with($this->exception, $operation);

        $this->denormalizationViolationFactory->handle($this->exception, $operation);
    }
}
