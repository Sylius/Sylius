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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelCodeCollection;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelCodeCollectionValidator;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ChannelCodeCollectionValidatorTest extends TestCase
{
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private MockObject&PropertyAccessorInterface $propertyAccessor;

    private ExecutionContextInterface&MockObject $executionContext;

    private ChannelCodeCollectionValidator $validator;

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new ChannelCodeCollectionValidator(
            $this->channelRepository,
            $this->propertyAccessor,
        );
        $this->validator->initialize($this->executionContext);
    }

    public function testItThrowsAnExceptionIfConstraintIsNotAnInstanceOfChannelCodeCollection(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $invalidConstraint = $this->createMock(Constraint::class);
        $this->validator->validate([], $invalidConstraint);
    }

    public function testItThrowsAnExceptionIfValueIsNotAnArray(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate('', new ChannelCodeCollection());
    }

    public function testItThrowsExceptionWhenValidatingUsingLocalChannelsAndObjectDoesNotImplementChannelsAwareInterface(): void
    {
        $this->executionContext->method('getObject')->willReturn(new \stdClass());

        $this->expectException(\LogicException::class);

        $this->validator->validate([], new ChannelCodeCollection([
            'validateAgainstAllChannels' => false,
            'channelAwarePropertyPath' => 'promotion',
        ]));
    }

    public function testItValidatesTheValueChannelsExistence(): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $channelsAware = $this->createMock(ChannelsAwareInterface::class);
        $form = $this->createMock(Form::class);

        $this->executionContext->method('getObject')->willReturn($form);
        $this->propertyAccessor->method('getValue')->willReturn($channelsAware);
        $channelsAware->method('getChannels')->willReturn(new ArrayCollection());

        $this->channelRepository->method('findAllWithBasicData')->willReturn([
            ['code' => 'WEB'], ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['does_not_exist' => ['one']];

        $constraint = new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'channelAwarePropertyPath' => 'shippingMethod',
        ]);

        $this->executionContext->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->invalidChannelMessage)
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ channel_code }}', 'does_not_exist')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate($value, $constraint);
    }

    public function testItRetrievesAnObjectFromValueAndValidatesCollectionsForLocalChannels(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);
        $channelsAware = $this->createMock(ChannelsAwareInterface::class);
        $form = $this->createMock(Form::class);

        $this->executionContext->method('getObject')->willReturn($form);
        $this->propertyAccessor->method('getValue')->willReturn($channelsAware);
        $channelsAware->method('getChannels')->willReturn(new ArrayCollection());

        $this->channelRepository->method('findAllWithBasicData')->willReturn([
            ['code' => 'WEB'], ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['WEB' => ['one'], 'MOBILE' => ['two']];

        $this->executionContext->expects($this->never())->method('buildViolation');
        $this->executionContext->method('getValidator')->willReturn($validator);
        $validator->method('inContext')->willReturn($contextualValidator);
        $contextualValidator->expects($this->once())->method('validate');

        $this->validator->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'channelAwarePropertyPath' => 'shippingMethod',
        ]));
    }

    public function testItValidatesCollectionsForChannelsFromValue(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);
        $channelsAware = $this->createMock(ChannelsAwareInterface::class);

        $this->executionContext->method('getObject')->willReturn($channelsAware);
        $this->propertyAccessor->method('getValue')->willReturn($channelsAware);
        $channelsAware->method('getChannels')->willReturn(new ArrayCollection());

        $this->channelRepository->method('findAllWithBasicData')->willReturn([
            ['code' => 'WEB'], ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['WEB' => ['one'], 'MOBILE' => ['two']];

        $this->executionContext->expects($this->never())->method('buildViolation');
        $this->executionContext->method('getValidator')->willReturn($validator);
        $validator->method('inContext')->willReturn($contextualValidator);
        $contextualValidator->expects($this->once())->method('validate');

        $this->validator->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'channelAwarePropertyPath' => 'promotion',
        ]));
    }

    public function testItValidatesCollectionsForLocalChannelsAndFromValue(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);
        $channelsAware = $this->createMock(ChannelsAwareInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->executionContext->method('getObject')->willReturn($channelsAware);
        $this->propertyAccessor->method('getValue')->willReturn($channelsAware);
        $channel->method('getCode')->willReturn('WEB');
        $channelsAware->method('getChannels')->willReturn(new ArrayCollection([$channel]));

        $this->channelRepository->method('findAllWithBasicData')->willReturn([
            ['code' => 'WEB'], ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['MOBILE' => ['two']];

        $this->executionContext->expects($this->never())->method('buildViolation');
        $this->executionContext->method('getValidator')->willReturn($validator);
        $validator->method('inContext')->willReturn($contextualValidator);
        $contextualValidator->expects($this->once())->method('validate');

        $this->validator->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'channelAwarePropertyPath' => 'promotion',
        ]));
    }

    public function testItDoesNothingWhenLocalCollectionIfChannelsIsEmpty(): void
    {
        $channelsAware = $this->createMock(ChannelsAwareInterface::class);

        $channelsAware->method('getChannels')->willReturn(new ArrayCollection());
        $this->executionContext->method('getObject')->willReturn($channelsAware);
        $this->propertyAccessor->method('getValue')->willReturn($channelsAware);

        $this->channelRepository->method('findAllWithBasicData')->willReturn([]);

        $this->executionContext->expects($this->never())->method('buildViolation');
        $this->executionContext->expects($this->never())->method('getValidator');

        $this->validator->validate([], new ChannelCodeCollection([
            'validateAgainstAllChannels' => false,
            'channelAwarePropertyPath' => 'promotion',
        ]));
    }

    public function testItValidatesCollectionsForAllChannels(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);

        $this->channelRepository->method('findAllWithBasicData')->willReturn([
            ['code' => 'WEB'], ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['WEB' => ['one'], 'MOBILE' => ['two']];

        $this->executionContext->method('getValidator')->willReturn($validator);
        $validator->method('inContext')->willReturn($contextualValidator);
        $contextualValidator->expects($this->once())->method('validate');

        $this->validator->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'validateAgainstAllChannels' => true,
        ]));
    }
}
