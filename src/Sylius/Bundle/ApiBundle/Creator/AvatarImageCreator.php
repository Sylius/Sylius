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

namespace Sylius\Bundle\ApiBundle\Creator;

use Sylius\Bundle\ApiBundle\Exception\AdminUserNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class AvatarImageCreator implements ImageCreatorInterface
{
    /**
     * @param FactoryInterface<AvatarImageInterface> $avatarImageFactory
     * @param RepositoryInterface<AdminUserInterface> $adminUserRepository
     */
    public function __construct(
        private FactoryInterface $avatarImageFactory,
        private RepositoryInterface $adminUserRepository,
        private ImageUploaderInterface $imageUploader,
    ) {
    }

    /** @param array<mixed> $context */
    public function create(string $ownerIdentifier, ?\SplFileInfo $file, ?string $type = null, array $context = []): ImageInterface
    {
        if (null === $file) {
            throw new NoFileUploadedException();
        }

        $owner = $this->adminUserRepository->find($ownerIdentifier);
        if (null === $owner) {
            throw new AdminUserNotFoundException();
        }

        $avatarImage = $this->avatarImageFactory->createNew();
        $avatarImage->setFile($file);

        $owner->setImage($avatarImage);

        $this->imageUploader->upload($avatarImage);

        return $avatarImage;
    }
}
