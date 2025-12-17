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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntity;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntityValidator;
use Sylius\Resource\Model\ToggleableInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class HasEnabledEntityValidatorTest extends TestCase
{
    private ManagerRegistry&MockObject $registry;

    private MockObject&PropertyAccessorInterface $accessor;

    private ExecutionContextInterface&MockObject $contextMock;

    private HasEnabledEntityValidator $hasEnabledEntityValidator;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->accessor = $this->createMock(PropertyAccessorInterface::class);
        $this->contextMock = $this->createMock(ExecutionContextInterface::class);
        $this->hasEnabledEntityValidator = new HasEnabledEntityValidator($this->registry, $this->accessor);
        $this->hasEnabledEntityValidator->initialize($this->contextMock);
    }

    public function testThrowsExceptionWhenConstraintIsNotAHasEnabledEntity(): void
    {
        $constraint = $this->createMock(Constraint::class);

        $this->accessor->expects($this->never())->method('getValue')->with($this->any());
        $this->registry->expects($this->never())->method('getManager');
        $this->contextMock->expects($this->never())->method('buildViolation')->with($this->any());

        $this->expectException(InvalidArgumentException::class);

        $this->hasEnabledEntityValidator->validate(null, $constraint);
    }

    public function testThrowsExceptionWhenValueIsNotAnObject(): void
    {
        $constraint = new HasEnabledEntity();

        $this->accessor->expects($this->never())->method('getValue')->with($this->any());
        $this->registry->expects($this->never())->method('getManager');
        $this->contextMock->expects($this->never())->method('buildViolation')->with($this->any());

        $this->expectException(InvalidArgumentException::class);

        $this->hasEnabledEntityValidator->validate(null, $constraint);
    }

    public function testDoesNothingWhenEntityIsEnabled(): void
    {
        $entity = $this->createMock(ToggleableInterface::class);
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';

        $this->accessor->expects($this->once())->method('getValue')->with($entity, 'enabled')->willReturn(true);
        $this->registry->expects($this->never())->method('getManager')->with($constraint->objectManager);
        $this->contextMock->expects($this->never())->method('buildViolation')->with($this->any());

        $this->hasEnabledEntityValidator->validate($entity, $constraint);
    }

    public function testThrowsExceptionWhenManagerSpecifiedByConstraintIsNotFound(): void
    {
        $entity = $this->createMock(ToggleableInterface::class);
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';

        $this->accessor->expects($this->once())->method('getValue')->with($entity, 'enabled')->willReturn(false);
        $this->registry->expects($this->once())->method('getManager')->with($constraint->objectManager)->willReturn(null);
        $this->contextMock->expects($this->never())->method('buildViolation')->with($this->any());

        $this->expectException(ConstraintDefinitionException::class);

        $this->hasEnabledEntityValidator->validate($entity, $constraint);
    }

    public function testThrowsExceptionWhenNoManagerIsSpecifiedByConstraintAndNoManagerCanBeFoundForValue(): void
    {
        $entity = $this->createMock(ToggleableInterface::class);
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = null;

        $this->accessor->expects($this->once())->method('getValue')->with($entity, 'enabled')->willReturn(false);
        $this->registry->expects($this->never())->method('getManager');
        $this->contextMock->expects($this->never())->method('buildViolation')->with($this->any());
        $this->registry->expects($this->once())->method('getManagerForClass')->with($entity::class)->willReturn(null);

        $this->expectException(ConstraintDefinitionException::class);

        $this->hasEnabledEntityValidator->validate($entity, $constraint);
    }

    public function testThrowsExceptionWhenEnabledFieldIsNeitherAMappedFieldOrAssociation(): void
    {
        $manager = $this->createMock(ObjectManager::class);
        $metadata = $this->createMock(ClassMetadata::class);
        $entity = $this->createMock(ToggleableInterface::class);
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';

        $this->accessor->expects($this->once())->method('getValue')->with($entity, 'enabled')->willReturn(false);
        $this->registry->expects($this->once())->method('getManager')->with('custom')->willReturn($manager);
        $manager->expects($this->once())->method('getClassMetadata')->with($entity::class)->willReturn($metadata);
        $metadata->expects($this->once())->method('hasField')->with('enabled')->willReturn(false);
        $metadata->expects($this->once())->method('hasAssociation')->with('enabled')->willReturn(false);
        $this->contextMock->expects($this->never())->method('buildViolation')->with($this->any());

        $this->expectException(ConstraintDefinitionException::class);

        $this->hasEnabledEntityValidator->validate($entity, $constraint);
    }

    public function testDoesNothingWhenPassedValueIsNotTheLastEnabledEntity(): void
    {
        $manager = $this->createMock(ObjectManager::class);
        $metadata = $this->createMock(ClassMetadata::class);
        $repository = $this->createMock(ObjectRepository::class);
        $entity = $this->createMock(ToggleableInterface::class);
        $anotherEntity = $this->createMock(ToggleableInterface::class);
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';

        $this->accessor->expects($this->once())->method('getValue')->with($entity, 'enabled')->willReturn(false);
        $this->registry->expects($this->once())->method('getManager')->with('custom')->willReturn($manager);
        $manager->expects($this->once())->method('getClassMetadata')->with($entity::class)->willReturn($metadata);
        $metadata->expects($this->once())->method('hasField')->with('enabled')->willReturn(true);
        $manager->expects($this->once())->method('getRepository')->with($entity::class)->willReturn($repository);
        $repository->expects($this->once())->method('findBy')->with(['enabled' => true])->willReturn([
            $entity,
            $anotherEntity,
        ]);

        $this->contextMock->expects($this->never())->method('buildViolation')->with($this->any());

        $this->hasEnabledEntityValidator->validate($entity, $constraint);
    }

    public function testAddsViolationIfPassedValueIsTheOnlyEnabledEntity(): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $manager = $this->createMock(ObjectManager::class);
        $metadata = $this->createMock(ClassMetadata::class);
        $repository = $this->createMock(ObjectRepository::class);
        $entity = $this->createMock(ToggleableInterface::class);
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';

        $this->accessor->expects($this->once())->method('getValue')->with($entity, 'enabled')->willReturn(false);
        $this->registry->expects($this->once())->method('getManager')->with('custom')->willReturn($manager);
        $manager->expects($this->once())->method('getClassMetadata')->with($entity::class)->willReturn($metadata);
        $metadata->expects($this->once())->method('hasField')->with('enabled')->willReturn(true);
        $manager->expects($this->once())->method('getRepository')->with($entity::class)->willReturn($repository);
        $repository->expects($this->once())->method('findBy')->with(['enabled' => true])->willReturn([
            $entity,
        ]);
        $this->contextMock->expects($this->once())->method('buildViolation')->with($constraint->message)->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('atPath')->with('enabled')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->hasEnabledEntityValidator->validate($entity, $constraint);
    }

    public function testAddsViolationAtCustomPathIfPassedValueIsTheOnlyEnabledEntity(): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $manager = $this->createMock(ObjectManager::class);
        $metadata = $this->createMock(ClassMetadata::class);
        $repository = $this->createMock(ObjectRepository::class);
        $entity = $this->createMock(ToggleableInterface::class);
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';
        $constraint->errorPath = 'customPath';

        $this->accessor->expects($this->once())->method('getValue')->with($entity, 'enabled')->willReturn(false);
        $this->registry->expects($this->once())->method('getManager')->with('custom')->willReturn($manager);
        $manager->expects($this->once())->method('getClassMetadata')->with($entity::class)->willReturn($metadata);
        $metadata->expects($this->once())->method('hasField')->with('enabled')->willReturn(true);
        $manager->expects($this->once())->method('getRepository')->with($entity::class)->willReturn($repository);
        $repository->expects($this->once())->method('findBy')->with(['enabled' => true])->willReturn([
            $entity,
        ]);
        $this->contextMock->expects($this->once())->method('buildViolation')->with($constraint->message)->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('atPath')->with('customPath')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->hasEnabledEntityValidator->validate($entity, $constraint);
    }
}
