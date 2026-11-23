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
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\AvatarImage\PersistProcessor;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Repository\AvatarImageRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AllowMockObjectsWithoutExpectations]
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
        /** @var AvatarImageInterface|MockObject $avatarImageMock */
        $avatarImageMock = $this->createMock(AvatarImageInterface::class);

        $operation = new Post(class: AvatarImageInterface::class);

        $request = new Request(attributes: ['id' => '1']);

        $file = new UploadedFile(__FILE__, basename(__FILE__), null, null, true);

        $request->files->set('file', $file);

        $this->avatarImageRepository->expects(self::never())->method('remove');

        $this->avatarImageCreator->expects(self::once())
            ->method('create')
            ->with('1', $file)
            ->willReturn($avatarImageMock);

        $this->processor->expects(self::once())
            ->method('process')
            ->with($avatarImageMock, $operation, [], ['request' => $request]);

        $this->persistProcessor->process(null, $operation, [], ['request' => $request]);
    }

    public function testRemovesOldAvatarImageDuringProcessingANewOne(): void
    {
        /** @var AvatarImageInterface|MockObject $oldAvatarImageMock */
        $oldAvatarImageMock = $this->createMock(AvatarImageInterface::class);
        /** @var AvatarImageInterface|MockObject $avatarImageMock */
        $avatarImageMock = $this->createMock(AvatarImageInterface::class);

        $operation = new Post(class: AvatarImageInterface::class);

        $request = new Request(attributes: ['id' => '1']);

        $file = new UploadedFile(__FILE__, basename(__FILE__), null, null, true);

        $request->files->set('file', $file);

        $this->avatarImageRepository->expects(self::once())->method('remove')->with($oldAvatarImageMock);

        $this->avatarImageCreator->expects(self::once())
            ->method('create')
            ->with('1', $file)
            ->willReturn($avatarImageMock);

        $this->processor->expects(self::once())
            ->method('process')
            ->with($avatarImageMock, $operation, [], ['request' => $request]);

        $this->persistProcessor->process($oldAvatarImageMock, $operation, [], ['request' => $request]);
    }

    public function testThrowsAValidationExceptionAndKeepsTheOldAvatarImageIfAnUploadedFileIsNotValid(): void
    {
        /** @var AvatarImageInterface|MockObject $oldAvatarImageMock */
        $oldAvatarImageMock = $this->createMock(AvatarImageInterface::class);
        /** @var ContextualValidatorInterface|MockObject $contextualValidatorMock */
        $contextualValidatorMock = $this->createMock(ContextualValidatorInterface::class);

        $operation = new Post(class: AvatarImageInterface::class, validationContext: ['groups' => ['sylius']]);

        $request = new Request(attributes: ['id' => '1']);

        $file = new UploadedFile(__FILE__, basename(__FILE__), null, null, true);

        $request->files->set('file', $file);

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

        $this->persistProcessor->process($oldAvatarImageMock, $operation, [], ['request' => $request]);
    }
}
