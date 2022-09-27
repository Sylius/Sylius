<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class AdminUserUpdater implements AdminUserUpdaterInterface
{
    public function __construct(
        private FactoryInterface $avatarImageFactory,
        private FileLocatorInterface $fileLocator,
        private ImageUploaderInterface $imageUploader,
    ) {
    }

    public function update(AdminUserInterface $adminUser, array $attributes): void
    {
        $adminUser->setEmail($attributes['email']);
        $adminUser->setUsername($attributes['username']);
        $adminUser->setEnabled($attributes['enabled']);
        $adminUser->setPlainPassword($attributes['password']);
        $adminUser->setLocaleCode($attributes['locale_code']);
        $adminUser->setFirstName($attributes['first_name']);
        $adminUser->setLastName($attributes['last_name']);

        if ($attributes['api']) {
            $adminUser->addRole('ROLE_API_ACCESS');
        }

        if ('' !== $attributes['avatar']) {
            $this->createAvatar($adminUser, $attributes);
        }
    }

    private function createAvatar(AdminUserInterface $adminUser, array $options): void
    {
        $imagePath = $this->fileLocator->locate($options['avatar']);
        $uploadedImage = new UploadedFile($imagePath, basename($imagePath));

        /** @var AvatarImageInterface $avatarImage */
        $avatarImage = $this->avatarImageFactory->createNew();

        $avatarImage->setFile($uploadedImage);

        $this->imageUploader->upload($avatarImage);

        $adminUser->setAvatar($avatarImage);
    }
}
