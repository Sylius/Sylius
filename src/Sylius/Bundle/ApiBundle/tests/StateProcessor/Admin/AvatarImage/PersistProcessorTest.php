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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\AvatarImage\PersistProcessor;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Repository\AvatarImageRepositoryInterface;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class PersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $processor;

    private ImageCreatorInterface&MockObject $avatarImageCreator;

    private AvatarImageRepositoryInterface&MockObject $avatarImageRepository;

    private PersistProcessor $persistProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->avatarImageCreator = $this->createMock(ImageCreatorInterface::class);
        $this->avatarImageRepository = $this->createMock(AvatarImageRepositoryInterface::class);
        $this->persistProcessor = new PersistProcessor(
            $this->processor,
            $this->avatarImageCreator,
            $this->avatarImageRepository,
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

        $operation = new Post();

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

        $operation = new Post();

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
}
