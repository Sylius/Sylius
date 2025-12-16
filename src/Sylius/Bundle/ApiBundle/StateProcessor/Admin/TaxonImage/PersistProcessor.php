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
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<TaxonImageInterface> */
final readonly class PersistProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private ImageCreatorInterface $taxonImageCreator,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::notInstanceOf($operation, DeleteOperationInterface::class);

        $image = $this->taxonImageCreator->create(
            $context['request']->attributes->get('code', ''),
            $context['request']->files->get('file'),
            $context['request']->request->get('type'),
        );

        return $this->processor->process($image, $operation, $uriVariables, $context);
    }
}
