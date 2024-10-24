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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\TaxonImage;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\TaxonImage\PersistProcessor;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class PersistProcessorSpec extends ObjectBehavior
{
    function let(ProcessorInterface $processor, ImageCreatorInterface $taxonImageCreator): void
    {
        $this->beConstructedWith($processor, $taxonImageCreator);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(PersistProcessor::class);
    }

    function it_implements_processor_interface(): void
    {
        $this->shouldImplement(ProcessorInterface::class);
    }

    function it_creates_and_processes_a_taxon_image(
        ProcessorInterface $processor,
        ImageCreatorInterface $taxonImageCreator,
        Request $request,
        ParameterBag $attributes,
        FileBag $files,
        TaxonImageInterface $taxonImage,
    ): void {
        $operation = new Post();

        $attributes->get('code', '')->willReturn('code');
        $request->attributes = $attributes;

        $file = new \SplFileInfo(__FILE__);
        $files->get('file')->willReturn($file);
        $request->files = $files;

        $request->request = new InputBag(['type' => 'type']);

        $taxonImageCreator->create('code', $file, 'type')->willReturn($taxonImage);

        $processor
            ->process($taxonImage->getWrappedObject(), $operation, [], ['request' => $request->getWrappedObject()])
            ->shouldBeCalled()
        ;

        $this->process(null, $operation, [], ['request' => $request->getWrappedObject()]);
    }

    function it_throws_an_exception_for_delete_operation(
        DeleteOperationInterface $deleteOperation,
        TaxonImageInterface $taxonImage,
    ): void {
        $deleteOperation->beADoubleOf(Operation::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('process', [$taxonImage, $deleteOperation, [], []]);
    }
}
