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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\AvatarImage;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\AvatarImage\PersistProcessor;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Repository\AvatarImageRepositoryInterface;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $processor;

    private ImageCreatorInterface&MockObject $avatarImageCreator;

    private AvatarImageRepositoryInterface&MockObject $avatarImageRepository;

    private MockObject&ValidatorInterface $validator;

    private PersistProcessor $persistProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->avatarImageCreator = $this->createMock(ImageCreatorInterface::class);
        $this->avatarImageRepository = $this->createMock(AvatarImageRepositoryInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->persistProcessor = new PersistProcessor(
            $this->processor,
            $this->avatarImageCreator,
            $this->avatarImageRepository,
            $this->validator,
        );
    }

    public function testCreatesAndProcessesAnAvatarImage(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var ParameterBag|MockObject $attributesMock */
        $attributesMock = $this->createMock(ParameterBag::class);
        /** @var FileBag|MockObject $filesMock */
        $filesMock = $this->createMock(FileBag::class);
        /** @var AvatarImageInterface|MockObject $avatarImageMock */
        $avatarImageMock = $this->createMock(AvatarImageInterface::class);

        $operation = new Post(class: AvatarImageInterface::class);

        $attributesMock->expects(self::once())
            ->method('getString')
            ->with('id')
            ->willReturn('1');

        $requestMock->attributes = $attributesMock;

        $file = new \SplFileInfo(__FILE__);

        $filesMock->expects(self::once())->method('get')->with('file')->willReturn($file);

        $requestMock->files = $filesMock;

        $this->avatarImageRepository->expects(self::never())->method('remove');

        $this->avatarImageCreator->expects(self::once())
            ->method('create')
            ->with('1', $file)
            ->willReturn($avatarImageMock);

        $this->processor->expects(self::once())
            ->method('process')
            ->with($avatarImageMock, $operation, [], ['request' => $requestMock]);

        $this->persistProcessor->process(null, $operation, [], ['request' => $requestMock]);
    }

    public function testRemovesOldAvatarImageDuringProcessingANewOne(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var ParameterBag|MockObject $attributesMock */
        $attributesMock = $this->createMock(ParameterBag::class);
        /** @var FileBag|MockObject $filesMock */
        $filesMock = $this->createMock(FileBag::class);
        /** @var AvatarImageInterface|MockObject $oldAvatarImageMock */
        $oldAvatarImageMock = $this->createMock(AvatarImageInterface::class);
        /** @var AvatarImageInterface|MockObject $avatarImageMock */
        $avatarImageMock = $this->createMock(AvatarImageInterface::class);

        $operation = new Post(class: AvatarImageInterface::class);

        $attributesMock->expects(self::once())
            ->method('getString')
            ->with('id')
            ->willReturn('1');

        $requestMock->attributes = $attributesMock;

        $file = new \SplFileInfo(__FILE__);

        $filesMock->expects(self::once())->method('get')->with('file')->willReturn($file);

        $requestMock->files = $filesMock;

        $this->avatarImageRepository->expects(self::once())->method('remove')->with($oldAvatarImageMock);

        $this->avatarImageCreator->expects(self::once())
            ->method('create')
            ->with('1', $file)
            ->willReturn($avatarImageMock);

        $this->processor->expects(self::once())
            ->method('process')
            ->with($avatarImageMock, $operation, [], ['request' => $requestMock]);

        $this->persistProcessor->process($oldAvatarImageMock, $operation, [], ['request' => $requestMock]);
    }

    public function testThrowsAValidationExceptionAndKeepsTheOldAvatarImageIfAnUploadedFileIsNotValid(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var FileBag|MockObject $filesMock */
        $filesMock = $this->createMock(FileBag::class);
        /** @var AvatarImageInterface|MockObject $oldAvatarImageMock */
        $oldAvatarImageMock = $this->createMock(AvatarImageInterface::class);
        /** @var ContextualValidatorInterface|MockObject $contextualValidatorMock */
        $contextualValidatorMock = $this->createMock(ContextualValidatorInterface::class);

        $operation = new Post(class: AvatarImageInterface::class, validationContext: ['groups' => ['sylius']]);

        $file = new \SplFileInfo(__FILE__);

        $filesMock->expects(self::once())->method('get')->with('file')->willReturn($file);

        $requestMock->files = $filesMock;

        $this->validator->expects(self::once())->method('startContext')->willReturn($contextualValidatorMock);

        $contextualValidatorMock->expects(self::once())->method('atPath')->with('file')->willReturnSelf();

        $contextualValidatorMock->expects(self::once())
            ->method('validatePropertyValue')
            ->with(AvatarImageInterface::class, 'file', $file, ['sylius'])
            ->willReturnSelf();

        $contextualValidatorMock->expects(self::once())
            ->method('getViolations')
            ->willReturn(new ConstraintViolationList([
                new ConstraintViolation('message', null, [], null, 'file', $file),
            ]));

        $this->avatarImageRepository->expects(self::never())->method('remove');

        $this->avatarImageCreator->expects(self::never())->method('create');

        $this->processor->expects(self::never())->method('process');

        $this->expectException(ValidationException::class);

        $this->persistProcessor->process($oldAvatarImageMock, $operation, [], ['request' => $requestMock]);
    }
}
