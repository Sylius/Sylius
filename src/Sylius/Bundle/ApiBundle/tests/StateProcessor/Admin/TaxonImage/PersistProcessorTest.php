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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\TaxonImage;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\TaxonImage\PersistProcessor;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

#[AllowMockObjectsWithoutExpectations]
final class PersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $processor;

    private ImageCreatorInterface&MockObject $taxonImageCreator;

    private PersistProcessor $persistProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->taxonImageCreator = $this->createMock(ImageCreatorInterface::class);
        $this->persistProcessor = new PersistProcessor($this->processor, $this->taxonImageCreator);
    }

    public function testImplementsProcessorInterface(): void
    {
        self::assertInstanceOf(ProcessorInterface::class, $this->persistProcessor);
    }

    public function testCreatesAndProcessesATaxonImage(): void
    {
        /** @var TaxonImageInterface|MockObject $taxonImageMock */
        $taxonImageMock = $this->createMock(TaxonImageInterface::class);

        $operation = new Post();

        $request = new Request(attributes: ['code' => 'code'], request: ['type' => 'type']);

        $file = new UploadedFile(__FILE__, basename(__FILE__), null, null, true);

        $request->files->set('file', $file);

        $this->taxonImageCreator->expects(self::once())
            ->method('create')
            ->with('code', $file, 'type')
            ->willReturn($taxonImageMock);

        $this->processor->expects(self::once())
            ->method('process')
            ->with($taxonImageMock, $operation, [], ['request' => $request]);

        $this->persistProcessor->process(null, $operation, [], ['request' => $request]);
    }

    public function testThrowsAnExceptionForDeleteOperation(): void
    {
        $deleteOperation = new Delete();
        /** @var TaxonImageInterface|MockObject $taxonImageMock */
        $taxonImageMock = $this->createMock(TaxonImageInterface::class);

        self::expectException(\InvalidArgumentException::class);

        $this->persistProcessor->process($taxonImageMock, $deleteOperation, [], []);
    }
}
