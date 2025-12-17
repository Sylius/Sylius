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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductImage;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<ProductImageInterface> */
final readonly class PersistProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private ImageCreatorInterface $productImageCreator,
        private ValidatorInterface $validator,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::notInstanceOf($operation, DeleteOperationInterface::class);

        $image = $this->productImageCreator->create(
            $context['request']->attributes->get('code', ''),
            $context['request']->files->get('file'),
            $context['request']->request->get('type'),
            ['productVariants' => $context['request']->request->all('productVariants')],
        );

        $violations = $this->validator->validate($image, null, $operation->getValidationContext()['groups'] ?? []);
        if (0 !== \count($violations)) {
            throw new ValidationException($violations);
        }

        return $this->processor->process($image, $operation, $uriVariables, $context);
    }
}
