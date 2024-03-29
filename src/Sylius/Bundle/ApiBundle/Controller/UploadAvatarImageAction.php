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

namespace Sylius\Bundle\ApiBundle\Controller;

use Sylius\Bundle\ApiBundle\Exception\AdminUserNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Repository\AvatarImageRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

final readonly class UploadAvatarImageAction
{
    public function __construct(
        private FactoryInterface $avatarImageFactory,
        private AvatarImageRepositoryInterface $avatarImageRepository,
        private RepositoryInterface $adminUserRepository,
        private ImageUploaderInterface $imageUploader,
    ) {
    }

    public function __invoke(Request $request): ImageInterface
    {
        /** @var AdminUserInterface $owner */
        $owner = $this->adminUserRepository->find($request->attributes->getString('id'));
        if (null === $owner) {
            throw new AdminUserNotFoundException();
        }

        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        if (null === $file) {
            throw new NoFileUploadedException();
        }

        /** @var ImageInterface $image */
        $image = $this->avatarImageFactory->createNew();
        $image->setFile($file);

        $oldImage = $owner->getImage();
        if ($oldImage !== null) {
            $this->avatarImageRepository->remove($oldImage);
        }
        $owner->setImage($image);

        $this->imageUploader->upload($image);

        return $image;
    }
}
