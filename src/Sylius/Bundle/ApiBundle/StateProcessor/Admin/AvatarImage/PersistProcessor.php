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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\AvatarImage;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Repository\AvatarImageRepositoryInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<AvatarImageInterface> */
final readonly class PersistProcessor implements ProcessorInterface
{
    /** @param AvatarImageRepositoryInterface<AvatarImageInterface> $avatarImageRepository */
    public function __construct(
        private ProcessorInterface $processor,
        private ImageCreatorInterface $avatarImageCreator,
        private AvatarImageRepositoryInterface $avatarImageRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::notInstanceOf($operation, DeleteOperationInterface::class);

        if ($data !== null) {
            $this->avatarImageRepository->remove($data);
        }

        $image = $this->avatarImageCreator->create(
            $context['request']->attributes->getString('id'),
            $context['request']->files->get('file'),
        );

        return $this->processor->process($image, $operation, $uriVariables, $context);
    }
}
