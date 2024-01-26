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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntity;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class HasEnabledEntityValidatorSpec extends ObjectBehavior
{
    public function let(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
    ): void {
        $this->beConstructedWith($registry, $accessor);

        $this->initialize($context);
    }

    public function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    public function it_throws_exception_when_constraint_is_not_a_has_enabled_entity(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
        Constraint $constraint,
    ): void {
        $accessor->getValue(Argument::cetera())->shouldNotBeCalled();
        $registry->getManager(Argument::any())->shouldNotBeCalled();
        $context->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', [null, $constraint]);
    }

    public function it_throws_exception_when_value_is_not_an_object(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
    ): void {
        $constraint = new HasEnabledEntity();
        $accessor->getValue(Argument::cetera())->shouldNotBeCalled();
        $registry->getManager(Argument::any())->shouldNotBeCalled();
        $context->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', [null, $constraint]);
    }

    public function it_does_nothing_when_entity_is_enabled(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
        ToggleableInterface $entity,
    ): void {
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';

        $accessor->getValue($entity, 'enabled')->willReturn(true);
        $registry->getManager($constraint->objectManager)->shouldNotBeCalled();
        $context->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate($entity, $constraint);
    }

    public function it_throws_exception_when_manager_specified_by_constraint_is_not_found(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
        ToggleableInterface $entity,
    ): void {
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';

        $accessor->getValue($entity, 'enabled')->willReturn(false);
        $registry->getManager($constraint->objectManager)->willReturn(null);
        $context->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(ConstraintDefinitionException::class)->during('validate', [$entity, $constraint]);
    }

    public function it_throws_exception_when_no_manager_is_specified_by_constraint_and_no_manager_can_be_found_for_value(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
        ToggleableInterface $entity,
    ): void {
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = null;

        $accessor->getValue($entity, 'enabled')->willReturn(false);
        $registry->getManager(Argument::any())->shouldNotBeCalled();
        $context->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $registry->getManagerForClass($entity->getWrappedObject()::class)->willReturn(null);

        $this->shouldThrow(ConstraintDefinitionException::class)->during('validate', [$entity, $constraint]);
    }

    public function it_throws_exception_when_enabled_field_is_neither_a_mapped_field_or_association(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
        ObjectManager $manager,
        ClassMetadata $metadata,
        ToggleableInterface $entity,
    ): void {
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';

        $accessor->getValue($entity, 'enabled')->willReturn(false);
        $registry->getManager('custom')->willReturn($manager);
        $manager->getClassMetadata($entity->getWrappedObject()::class)->willReturn($metadata);

        $metadata->hasField('enabled')->willReturn(false);
        $metadata->hasAssociation('enabled')->willReturn(false);

        $context->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(ConstraintDefinitionException::class)->during('validate', [$entity, $constraint]);
    }

    public function it_does_nothing_when_passed_value_is_not_the_last_enabled_entity(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
        ObjectManager $manager,
        ClassMetadata $metadata,
        ObjectRepository $repository,
        ToggleableInterface $entity,
        ToggleableInterface $anotherEntity,
    ): void {
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';

        $accessor->getValue($entity, 'enabled')->willReturn(false);
        $registry->getManager('custom')->willReturn($manager);
        $manager->getClassMetadata($entity->getWrappedObject()::class)->willReturn($metadata);
        $metadata->hasField('enabled')->willReturn(true);

        $manager->getRepository($entity->getWrappedObject()::class)->willReturn($repository);

        $repository->findBy(['enabled' => true])->willReturn([
            $entity->getWrappedObject(),
            $anotherEntity->getWrappedObject(),
        ]);

        $context->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate($entity, $constraint);
    }

    public function it_adds_violation_if_passed_value_is_the_only_enabled_entity(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder,
        ObjectManager $manager,
        ClassMetadata $metadata,
        ObjectRepository $repository,
        ToggleableInterface $entity,
    ): void {
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';

        $accessor->getValue($entity, 'enabled')->willReturn(false);
        $registry->getManager('custom')->willReturn($manager);
        $manager->getClassMetadata($entity->getWrappedObject()::class)->willReturn($metadata);
        $metadata->hasField('enabled')->willReturn(true);

        $manager->getRepository($entity->getWrappedObject()::class)->willReturn($repository);

        $repository->findBy(['enabled' => true])->willReturn([
            $entity->getWrappedObject(),
        ]);

        $context->buildViolation($constraint->message)->willReturn($violationBuilder);
        $violationBuilder->atPath('enabled')->shouldBeCalled()->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $this->validate($entity, $constraint);
    }

    public function it_adds_violation_at_custom_path_if_passed_value_is_the_only_enabled_entity(
        ManagerRegistry $registry,
        PropertyAccessorInterface $accessor,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder,
        ObjectManager $manager,
        ClassMetadata $metadata,
        ObjectRepository $repository,
        ToggleableInterface $entity,
    ): void {
        $constraint = new HasEnabledEntity();
        $constraint->enabledPath = 'enabled';
        $constraint->objectManager = 'custom';
        $constraint->errorPath = 'customPath';

        $accessor->getValue($entity, 'enabled')->willReturn(false);
        $registry->getManager('custom')->willReturn($manager);
        $manager->getClassMetadata($entity->getWrappedObject()::class)->willReturn($metadata);
        $metadata->hasField('enabled')->willReturn(true);

        $manager->getRepository($entity->getWrappedObject()::class)->willReturn($repository);

        $repository->findBy(['enabled' => true])->willReturn([
            $entity->getWrappedObject(),
        ]);

        $context->buildViolation($constraint->message)->willReturn($violationBuilder);
        $violationBuilder->atPath('customPath')->shouldBeCalled()->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $this->validate($entity, $constraint);
    }
}
