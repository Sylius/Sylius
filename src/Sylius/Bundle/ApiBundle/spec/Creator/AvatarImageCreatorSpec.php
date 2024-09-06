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

namespace spec\Sylius\Bundle\ApiBundle\Creator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Exception\AdminUserNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class AvatarImageCreatorSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $avatarImageFactory,
        RepositoryInterface $adminUserRepository,
        ImageUploaderInterface $imageUploader,
    ) {
        $this->beConstructedWith($avatarImageFactory, $adminUserRepository, $imageUploader);
    }

    function it_creates_an_avatar_image(
        FactoryInterface $avatarImageFactory,
        RepositoryInterface $adminUserRepository,
        ImageUploaderInterface $imageUploader,
        AdminUserInterface $adminUser,
        AvatarImageInterface $avatarImage,
    ): void {
        $file = new \SplFileInfo(__FILE__);

        $adminUserRepository->find('1')->willReturn($adminUser);

        $avatarImageFactory->createNew()->willReturn($avatarImage);
        $avatarImage->setFile($file)->shouldBeCalled();

        $adminUser->setImage($avatarImage)->shouldBeCalled();

        $imageUploader->upload($avatarImage)->shouldBeCalled();

        $this->create('1', $file, null)->shouldReturn($avatarImage);
    }

    function it_throws_an_exception_if_admin_user_is_not_found(
        FactoryInterface $avatarImageFactory,
        RepositoryInterface $adminUserRepository,
        ImageUploaderInterface $imageUploader,
    ): void {
        $file = new \SplFileInfo(__FILE__);

        $adminUserRepository->find('1')->willReturn(null);

        $avatarImageFactory->createNew()->shouldNotBeCalled();
        $imageUploader->upload(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(AdminUserNotFoundException::class)
            ->during('create', ['1', $file, null])
        ;
    }

    function it_throws_an_exception_if_there_is_no_uploaded_file(
        FactoryInterface $avatarImageFactory,
        RepositoryInterface $adminUserRepository,
        ImageUploaderInterface $imageUploader,
    ): void {
        $adminUserRepository->find('1')->shouldNotBeCalled();
        $avatarImageFactory->createNew()->shouldNotBeCalled();
        $imageUploader->upload(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(NoFileUploadedException::class)
            ->during('create', ['1', null, null])
        ;
    }
}
