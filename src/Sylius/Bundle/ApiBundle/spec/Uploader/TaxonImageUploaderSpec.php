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

namespace spec\Sylius\Bundle\ApiBundle\Uploader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Bundle\ApiBundle\Exception\TaxonImageNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\TaxonNotFoundException;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\TaxonImageRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class TaxonImageUploaderSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $taxonImageFactory,
        TaxonImageRepositoryInterface $taxonImageRepository,
        TaxonRepositoryInterface $taxonRepository,
        ImageUploaderInterface $imageUploader,
    ) {
        $this->beConstructedWith($taxonImageFactory, $taxonImageRepository, $taxonRepository, $imageUploader);
    }

    function it_creates_taxon_image(
        FactoryInterface $taxonImageFactory,
        TaxonRepositoryInterface $taxonRepository,
        ImageUploaderInterface $imageUploader,
        TaxonInterface $taxon,
        TaxonImageInterface $taxonImage,
    ): void {
        $file = new \SplFileInfo(__FILE__);

        $taxonRepository->findOneBy(['code' => 'CODE'])->willReturn($taxon);

        $taxonImageFactory->createNew()->willReturn($taxonImage);
        $taxonImage->setFile($file)->shouldBeCalled();
        $taxonImage->setType('banner')->shouldBeCalled();

        $taxon->addImage($taxonImage)->shouldBeCalled();

        $imageUploader->upload($taxonImage)->shouldBeCalled();

        $this->create('CODE', $file, 'banner')->shouldReturn($taxonImage);
    }

    function it_throws_an_exception_if_taxon_is_not_found_when_creating(
        FactoryInterface $taxonImageFactory,
        TaxonRepositoryInterface $taxonRepository,
        ImageUploaderInterface $imageUploader,
    ): void {
        $file = new \SplFileInfo(__FILE__);

        $taxonRepository->findOneBy(['code' => 'CODE'])->willReturn(null);

        $taxonImageFactory->createNew()->shouldNotBeCalled();
        $imageUploader->upload(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(TaxonNotFoundException::class)
            ->during('create', ['CODE', $file, 'banner'])
        ;
    }

    function it_throws_an_exception_if_there_is_no_uploaded_file_when_creating(
        FactoryInterface $taxonImageFactory,
        TaxonRepositoryInterface $taxonRepository,
        ImageUploaderInterface $imageUploader,
    ): void {
        $taxonRepository->findOneBy(['code' => 'CODE'])->shouldNotBeCalled();
        $taxonImageFactory->createNew()->shouldNotBeCalled();
        $imageUploader->upload(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(NoFileUploadedException::class)
            ->during('create', ['CODE', null, 'banner'])
        ;
    }

    function it_modifies_the_existing_taxon_image(
        TaxonImageRepositoryInterface $taxonImageRepository,
        ImageUploaderInterface $imageUploader,
        TaxonImageInterface $taxonImage,
    ): void {
        $file = new \SplFileInfo(__FILE__);

        $taxonImageRepository->findOneByIdAndOwnerCode('11', 'CODE')->willReturn($taxonImage);

        $taxonImage->setFile($file)->shouldBeCalled();
        $taxonImage->setType('banner')->shouldBeCalled();

        $imageUploader->upload($taxonImage)->shouldBeCalled();

        $this->modify('CODE', '11', $file, 'banner')->shouldReturn($taxonImage);
    }

    function it_does_not_upload_file_if_it_has_not_been_provided_when_editing(
        TaxonImageRepositoryInterface $taxonImageRepository,
        ImageUploaderInterface $imageUploader,
        TaxonImageInterface $taxonImage,
    ): void {
        $file = new \SplFileInfo(__FILE__);

        $taxonImageRepository->findOneByIdAndOwnerCode('11', 'CODE')->willReturn($taxonImage);

        $taxonImage->setType('banner')->shouldBeCalled();

        $taxonImage->setFile($file)->shouldNotBeCalled();
        $imageUploader->upload($taxonImage)->shouldNotBeCalled();

        $this->modify('CODE', '11', null, 'banner')->shouldReturn($taxonImage);
    }

    function it_throws_an_exception_if_taxon_image_is_not_found_when_editing(
        TaxonImageRepositoryInterface $taxonImageRepository,
        ImageUploaderInterface $imageUploader,
    ): void {
        $file = new \SplFileInfo(__FILE__);

        $taxonImageRepository->findOneByIdAndOwnerCode('11', 'CODE')->willReturn(null);

        $imageUploader->upload(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(TaxonImageNotFoundException::class)
            ->during('modify', ['CODE', '11', $file, 'banner'])
        ;
    }
}
