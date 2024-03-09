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

use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Bundle\ApiBundle\Exception\TaxonNotFoundException;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class TaxonImageCreator implements ImageCreatorInterface
{
    /**
     * @param FactoryInterface<TaxonImageInterface> $taxonImageFactory
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        private FactoryInterface $taxonImageFactory,
        private TaxonRepositoryInterface $taxonRepository,
        private ImageUploaderInterface $imageUploader,
    ) {
    }

    /** @param array<mixed> $context */
    public function create(string $ownerCode, ?\SplFileInfo $file, ?string $type, array $context = []): ImageInterface
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
}
