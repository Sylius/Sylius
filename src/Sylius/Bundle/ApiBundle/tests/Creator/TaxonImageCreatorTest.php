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

namespace Tests\Sylius\Bundle\ApiBundle\Creator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Sylius\Bundle\ApiBundle\Creator\TaxonImageCreator;
use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Bundle\ApiBundle\Exception\TaxonNotFoundException;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class TaxonImageCreatorTest extends TestCase
{
    private FactoryInterface&MockObject $taxonImageFactory;

    private MockObject&TaxonRepositoryInterface $taxonRepository;

    private ImageUploaderInterface&MockObject $imageUploader;

    private TaxonImageCreator $taxonImageCreator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxonImageFactory = $this->createMock(FactoryInterface::class);
        $this->taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $this->imageUploader = $this->createMock(ImageUploaderInterface::class);
        $this->taxonImageCreator = new TaxonImageCreator(
            $this->taxonImageFactory,
            $this->taxonRepository,
            $this->imageUploader,
        );
    }

    public function testCreatesTaxonImage(): void
    {
        /** @var TaxonInterface&MockObject $taxon */
        $taxon = $this->createMock(TaxonInterface::class);
        /** @var TaxonImageInterface&MockObject $taxonImage */
        $taxonImage = $this->createMock(TaxonImageInterface::class);

        $file = new SplFileInfo(__FILE__);

        $this->taxonRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CODE'])
            ->willReturn($taxon);

        $this->taxonImageFactory->expects(self::once())->method('createNew')->willReturn($taxonImage);

        $taxonImage->expects(self::once())->method('setFile')->with($file);

        $taxonImage->expects(self::once())->method('setType')->with('banner');

        $taxon->expects(self::once())->method('addImage')->with($taxonImage);

        $this->imageUploader->expects(self::once())->method('upload')->with($taxonImage);

        self::assertSame($taxonImage, $this->taxonImageCreator->create('CODE', $file, 'banner'));
    }

    public function testThrowsAnExceptionIfTaxonIsNotFound(): void
    {
        $file = new SplFileInfo(__FILE__);

        $this->taxonRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CODE'])
            ->willReturn(null);

        $this->taxonImageFactory->expects(self::never())->method('createNew');

        $this->imageUploader->expects(self::never())->method('upload');

        self::expectException(TaxonNotFoundException::class);

        $this->taxonImageCreator->create('CODE', $file, 'banner');
    }

    public function testThrowsAnExceptionIfThereIsNoUploadedFile(): void
    {
        $this->taxonRepository->expects(self::never())->method('findOneBy')->with(['code' => 'CODE']);

        $this->taxonImageFactory->expects(self::never())->method('createNew');

        $this->imageUploader->expects(self::never())->method('upload');

        self::expectException(NoFileUploadedException::class);

        $this->taxonImageCreator->create('CODE', null, 'banner');
    }
}
