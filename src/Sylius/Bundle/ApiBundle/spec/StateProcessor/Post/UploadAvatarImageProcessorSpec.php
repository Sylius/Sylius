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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Post;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Repository\AvatarImageRepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class UploadAvatarImageProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $processor,
        ImageCreatorInterface $avatarImageCreator,
        AvatarImageRepositoryInterface $avatarImageRepository,
    ): void {
        $this->beConstructedWith($processor, $avatarImageCreator, $avatarImageRepository);
    }

    function it_creates_and_processes_an_avatar_image(
        ProcessorInterface $processor,
        ImageCreatorInterface $avatarImageCreator,
        AvatarImageRepositoryInterface $avatarImageRepository,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $files,
        AvatarImageInterface $avatarImage,
    ): void {
        $operation = new Post();

        $attributes->getString('id')->willReturn('1');
        $request->attributes = $attributes;

        $file = new \SplFileInfo(__FILE__);
        $files->get('file')->willReturn($file);
        $request->files = $files;

        $avatarImageRepository->remove(Argument::any())->shouldNotBeCalled();
        $avatarImageCreator->create('1', $file, null)->willReturn($avatarImage);

        $processor
            ->process($avatarImage->getWrappedObject(), $operation, [], ['request' => $request->getWrappedObject()])
            ->shouldBeCalled()
        ;

        $this->process(null, $operation, [], ['request' => $request->getWrappedObject()]);
    }

    function it_removes_old_avatar_image_during_processing_a_new_one(
        ProcessorInterface $processor,
        ImageCreatorInterface $avatarImageCreator,
        AvatarImageRepositoryInterface $avatarImageRepository,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $files,
        AvatarImageInterface $oldAvatarImage,
        AvatarImageInterface $avatarImage,
    ): void {
        $operation = new Post();

        $attributes->getString('id')->willReturn('1');
        $request->attributes = $attributes;

        $file = new \SplFileInfo(__FILE__);
        $files->get('file')->willReturn($file);
        $request->files = $files;

        $avatarImageRepository->remove($oldAvatarImage)->shouldBeCalled();
        $avatarImageCreator->create('1', $file, null)->willReturn($avatarImage);

        $processor
            ->process($avatarImage->getWrappedObject(), $operation, [], ['request' => $request->getWrappedObject()])
            ->shouldBeCalled()
        ;

        $this->process($oldAvatarImage, $operation, [], ['request' => $request->getWrappedObject()]);
    }
}
