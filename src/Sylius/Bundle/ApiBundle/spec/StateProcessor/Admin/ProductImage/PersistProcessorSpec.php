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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductImage;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductImage\PersistProcessor;
use Sylius\Component\Core\Model\ProductImageInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PersistProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $processor,
        ImageCreatorInterface $productImageCreator,
        ValidatorInterface $validator,
    ): void {
        $this->beConstructedWith($processor, $productImageCreator, $validator);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(PersistProcessor::class);
    }

    function it_implements_processor_interface(): void
    {
        $this->shouldImplement(ProcessorInterface::class);
    }

    function it_creates_and_processes_a_product_image(
        ProcessorInterface $processor,
        ImageCreatorInterface $productImageCreator,
        ValidatorInterface $validator,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $files,
        ParameterBag $requestParams,
        ProductImageInterface $productImage,
        ConstraintViolationListInterface $constraintViolationList,
    ): void {
        $operation = new Post(validationContext: ['groups' => ['sylius']]);

        $attributes->get('code', '')->willReturn('code');
        $request->attributes = $attributes;

        $file = new \SplFileInfo(__FILE__);
        $files->get('file')->willReturn($file);
        $request->files = $files;

        $requestParams->get('type')->willReturn('type');
        $requestParams->all('productVariants')->willReturn(['/api/v2/admin/product-variants/MUG']);
        $request->request = $requestParams;

        $productImageCreator
            ->create('code', $file, 'type', ['productVariants' => ['/api/v2/admin/product-variants/MUG']])
            ->willReturn($productImage)
        ;

        $validator->validate($productImage, null, ['sylius'])->willReturn($constraintViolationList);
        $constraintViolationList->count()->willReturn(0);

        $processor
            ->process($productImage->getWrappedObject(), $operation, [], ['request' => $request->getWrappedObject()])
            ->shouldBeCalled()
        ;

        $this->process(null, $operation, [], ['request' => $request->getWrappedObject()]);
    }

    function it_throws_a_validation_exception_if_a_created_product_image_is_not_valid(
        ProcessorInterface $processor,
        ImageCreatorInterface $productImageCreator,
        ValidatorInterface $validator,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $files,
        ParameterBag $requestParams,
        ProductImageInterface $productImage,
        ConstraintViolationListInterface $constraintViolationList,
        ConstraintViolationInterface $constraintViolation,
    ): void {
        $operation = new Post(validationContext: ['groups' => ['sylius']]);

        $attributes->get('code', '')->willReturn('code');
        $request->attributes = $attributes;

        $file = new \SplFileInfo(__FILE__);
        $files->get('file')->willReturn($file);
        $request->files = $files;

        $requestParams->get('type')->willReturn('type');
        $requestParams->all('productVariants')->willReturn(['/api/v2/admin/product-variants/MUG']);
        $request->request = $requestParams;

        $productImageCreator
            ->create('code', $file, 'type', ['productVariants' => ['/api/v2/admin/product-variants/MUG']])
            ->willReturn($productImage)
        ;

        $validator->validate($productImage, null, ['sylius'])->willReturn($constraintViolationList);

        $constraintViolationList->count()->willReturn(1);
        $constraintViolationList->rewind()->shouldBeCalled();
        $constraintViolationList->valid()->willReturn(false);
        $constraintViolationList->current()->willReturn($constraintViolation);
        $constraintViolation->getPropertyPath()->willReturn('productVariants');
        $constraintViolation->getMessage()->willReturn('message');

        $processor
            ->process($productImage->getWrappedObject(), $operation, [], ['request' => $request->getWrappedObject()])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(ValidationException::class)
            ->during('process', [$productImage, $operation, [], ['request' => $request->getWrappedObject()]])
        ;
    }

    function it_throws_an_exception_for_delete_operation(
        DeleteOperationInterface $deleteOperation,
        ProductImageInterface $productImage,
    ): void {
        $deleteOperation->beADoubleOf(Operation::class);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [$productImage, $deleteOperation, [], []])
        ;
    }
}
