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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\TaxonImage;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<TaxonImageInterface> */
final readonly class PersistProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private ImageCreatorInterface $taxonImageCreator,
        private ValidatorInterface $validator,
        private string $taxonImageClass,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::notInstanceOf($operation, DeleteOperationInterface::class);

        $file = $context['request']->files->get('file');

        $violations = $this->validator
            ->startContext()
            ->atPath('file')
            ->validatePropertyValue($this->taxonImageClass, 'file', $file, $operation->getValidationContext()['groups'] ?? [])
            ->getViolations()
        ;
        if (0 !== \count($violations)) {
            throw new ValidationException($violations);
        }

        $image = $this->taxonImageCreator->create(
            $context['request']->attributes->get('code', ''),
            $file,
            $context['request']->request->get('type'),
        );

        return $this->processor->process($image, $operation, $uriVariables, $context);
    }
}
