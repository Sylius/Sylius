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
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/** @experimental */
final class TaxonImageCreator implements TaxonImageCreatorInterface
{
    public function __construct(
        private FactoryInterface $taxonImageFactory,
        private TaxonRepositoryInterface $taxonRepository,
        private ImageUploaderInterface $imageUploader,
    ) {
    }

    public function create(string $taxonCode, ?\SplFileInfo $file, ?string $type): TaxonImageInterface
    {
        if (null === $file) {
            throw new NoFileUploadedException();
        }

        /** @var TaxonInterface|null $owner */
        $owner = $this->taxonRepository->findOneBy(['code' => $taxonCode]);
        if (null === $owner) {
            throw new TaxonNotFoundException();
        }

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $this->taxonImageFactory->createNew();
        $taxonImage->setFile($file);
        $taxonImage->setType($type);

        $owner->addImage($taxonImage);

        $this->imageUploader->upload($taxonImage);

        return $taxonImage;
    }
}
