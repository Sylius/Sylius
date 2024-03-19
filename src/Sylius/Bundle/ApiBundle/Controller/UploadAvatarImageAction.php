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

use ApiPlatform\Api\IriConverterInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Repository\AvatarImageRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class UploadAvatarImageAction
{
    public function __construct(
        private FactoryInterface $avatarImageFactory,
        private AvatarImageRepositoryInterface $avatarImageRepository,
        private ImageUploaderInterface $imageUploader,
        private IriConverterInterface $iriConverter,
    ) {
    }

    public function __invoke(Request $request): ImageInterface
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        /** @var ImageInterface $image */
        $image = $this->avatarImageFactory->createNew();
        $image->setFile($file);

        /** @var string $ownerIri */
        $ownerIri = $request->request->get('owner');
        Assert::notEmpty($ownerIri);

        /** @var ResourceInterface|AdminUserInterface $owner */
        $owner = $this->iriConverter->getResourceFromIri($ownerIri);
        Assert::isInstanceOf($owner, AdminUserInterface::class);

        $oldImage = $owner->getImage();
        if ($oldImage !== null) {
            $this->avatarImageRepository->remove($oldImage);
        }
        $owner->setImage($image);

        $this->imageUploader->upload($image);

        return $image;
    }
}
