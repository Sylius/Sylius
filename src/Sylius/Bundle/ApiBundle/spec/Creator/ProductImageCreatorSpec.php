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
use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Bundle\ApiBundle\Exception\ProductNotFoundException;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductImageCreatorSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $productImageFactory,
        ProductRepositoryInterface $productRepository,
        ImageUploaderInterface $imageUploader,
    ) {
        $this->beConstructedWith($productImageFactory, $productRepository, $imageUploader);
    }

    function it_creates_product_image(
        FactoryInterface $productImageFactory,
        ProductRepositoryInterface $productRepository,
        ImageUploaderInterface $imageUploader,
        ProductInterface $product,
        ProductImageInterface $productImage,
    ): void {
        $file = new \SplFileInfo(__FILE__);

        $productRepository->findOneBy(['code' => 'CODE'])->willReturn($product);

        $productImageFactory->createNew()->willReturn($productImage);
        $productImage->setFile($file)->shouldBeCalled();
        $productImage->setType('banner')->shouldBeCalled();

        $product->addImage($productImage)->shouldBeCalled();

        $imageUploader->upload($productImage)->shouldBeCalled();

        $this->create('CODE', $file, 'banner')->shouldReturn($productImage);
    }

    function it_throws_an_exception_if_product_is_not_found(
        FactoryInterface $productImageFactory,
        ProductRepositoryInterface $productRepository,
        ImageUploaderInterface $imageUploader,
    ): void {
        $file = new \SplFileInfo(__FILE__);

        $productRepository->findOneBy(['code' => 'CODE'])->willReturn(null);

        $productImageFactory->createNew()->shouldNotBeCalled();
        $imageUploader->upload(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(ProductNotFoundException::class)
            ->during('create', ['CODE', $file, 'banner'])
        ;
    }

    function it_throws_an_exception_if_there_is_no_uploaded_file(
        FactoryInterface $productImageFactory,
        ProductRepositoryInterface $productRepository,
        ImageUploaderInterface $imageUploader,
    ): void {
        $productRepository->findOneBy(['code' => 'CODE'])->shouldNotBeCalled();
        $productImageFactory->createNew()->shouldNotBeCalled();
        $imageUploader->upload(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(NoFileUploadedException::class)
            ->during('create', ['CODE', null, 'banner'])
        ;
    }
}
