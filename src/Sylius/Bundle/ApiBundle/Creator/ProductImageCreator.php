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

use ApiPlatform\Api\IriConverterInterface;
use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Bundle\ApiBundle\Exception\ProductNotFoundException;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ProductImageCreator implements ImageCreatorInterface
{
    /**
     * @param FactoryInterface<ProductImageInterface> $productImageFactory
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     */
    public function __construct(
        private FactoryInterface $productImageFactory,
        private ProductRepositoryInterface $productRepository,
        private ImageUploaderInterface $imageUploader,
        private IriConverterInterface $iriConverter,
    ) {
    }

    /** @param array<mixed> $context */
    public function create(string $ownerCode, ?\SplFileInfo $file, ?string $type, array $context = []): ImageInterface
    {
        if (null === $file) {
            throw new NoFileUploadedException();
        }

        $owner = $this->productRepository->findOneBy(['code' => $ownerCode]);
        if (null === $owner) {
            throw new ProductNotFoundException();
        }

        $productImage = $this->productImageFactory->createNew();
        $productImage->setFile($file);
        $productImage->setType($type);

        if ($context['productVariants']) {
            $this->setProductVariants($productImage, $context['productVariants']);
        }

        $owner->addImage($productImage);

        $this->imageUploader->upload($productImage);

        return $productImage;
    }

    /** @param array<string> $productVariantsIris */
    private function setProductVariants(ProductImageInterface $productImage, array $productVariantsIris): void
    {
        foreach ($productVariantsIris as $productVariantIri) {
            $productVariant = $this->iriConverter->getResourceFromIri($productVariantIri);
            Assert::isInstanceOf($productVariant, ProductVariantInterface::class);
            $productImage->addProductVariant($productVariant);
        }
    }
}
