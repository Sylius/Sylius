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

namespace Tests\Sylius\Bundle\ApiBundle\Creator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Sylius\Bundle\ApiBundle\Creator\AvatarImageCreator;
use Sylius\Bundle\ApiBundle\Exception\AdminUserNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class AvatarImageCreatorTest extends TestCase
{
    private FactoryInterface&MockObject $avatarImageFactory;

    private MockObject&RepositoryInterface $adminUserRepository;

    private ImageUploaderInterface&MockObject $imageUploader;

    private AvatarImageCreator $avatarImageCreator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->avatarImageFactory = $this->createMock(FactoryInterface::class);
        $this->adminUserRepository = $this->createMock(RepositoryInterface::class);
        $this->imageUploader = $this->createMock(ImageUploaderInterface::class);
        $this->avatarImageCreator = new AvatarImageCreator(
            $this->avatarImageFactory,
            $this->adminUserRepository,
            $this->imageUploader,
        );
    }

    public function testCreatesAnAvatarImage(): void
    {
        /** @var AdminUserInterface&MockObject $adminUser */
        $adminUser = $this->createMock(AdminUserInterface::class);
        /** @var AvatarImageInterface&MockObject $avatarImage */
        $avatarImage = $this->createMock(AvatarImageInterface::class);

        $file = new SplFileInfo(__FILE__);

        $this->adminUserRepository->expects(self::once())->method('find')->with('1')->willReturn($adminUser);

        $this->avatarImageFactory->expects(self::once())->method('createNew')->willReturn($avatarImage);

        $avatarImage->expects(self::once())->method('setFile')->with($file);

        $adminUser->expects(self::once())->method('setImage')->with($avatarImage);

        $this->imageUploader->expects(self::once())->method('upload')->with($avatarImage);

        self::assertSame($avatarImage, $this->avatarImageCreator->create('1', $file, null));
    }

    public function testThrowsAnExceptionIfAdminUserIsNotFound(): void
    {
        $file = new SplFileInfo(__FILE__);

        $this->adminUserRepository->expects(self::once())->method('find')->with('1')->willReturn(null);

        $this->avatarImageFactory->expects(self::never())->method('createNew');

        $this->imageUploader->expects(self::never())->method('upload');

        self::expectException(AdminUserNotFoundException::class);

        $this->avatarImageCreator->create('1', $file, null);
    }

    public function testThrowsAnExceptionIfThereIsNoUploadedFile(): void
    {
        $this->adminUserRepository->expects(self::never())->method('find')->with('1');

        $this->avatarImageFactory->expects(self::never())->method('createNew');

        $this->imageUploader->expects(self::never())->method('upload');

        self::expectException(NoFileUploadedException::class);

        $this->avatarImageCreator->create('1', null, null);
    }
}
