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

namespace Sylius\Bundle\ApiBundle\Uploader;

use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Bundle\ApiBundle\Exception\TaxonImageNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\TaxonNotFoundException;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\TaxonImageRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface as CoreImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/** @experimental */
final class TaxonImageUploader implements ImageUploaderInterface
{
    /**
     * @param FactoryInterface<TaxonImageInterface> $taxonImageFactory
     * @param TaxonImageRepositoryInterface<TaxonImageInterface> $taxonImageRepository
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        private FactoryInterface $taxonImageFactory,
        private TaxonImageRepositoryInterface $taxonImageRepository,
        private TaxonRepositoryInterface $taxonRepository,
        private CoreImageUploaderInterface $imageUploader,
    ) {
    }

    public function create(string $ownerCode, ?\SplFileInfo $file, ?string $type): TaxonImageInterface
    {
        if (null === $file) {
            throw new NoFileUploadedException();
        }

        $owner = $this->taxonRepository->findOneBy(['code' => $ownerCode]);
        if (null === $owner) {
            throw new TaxonNotFoundException();
        }

        $taxonImage = $this->taxonImageFactory->createNew();
        $taxonImage->setFile($file);
        $taxonImage->setType($type);

        $owner->addImage($taxonImage);

        $this->imageUploader->upload($taxonImage);

        return $taxonImage;
    }

    public function modify(string $ownerCode, string $imageId, ?\SplFileInfo $file, ?string $type): TaxonImageInterface
    {
        $taxonImage = $this->taxonImageRepository->findOneByIdAndOwnerCode($imageId, $ownerCode);
        if (null === $taxonImage) {
            throw new TaxonImageNotFoundException();
        }

        if (null !== $file) {
            $taxonImage->setFile($file);
            $this->imageUploader->upload($taxonImage);
        }

        $taxonImage->setType($type);

        return $taxonImage;
    }
}
