<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Sylius\Bundle\ApiBundle\Command\CommandAwareDataTransformerInterface;

/** @experimental */
final class CommandAwareInputDataTransformer implements DataTransformerInterface
{
    /** @var CommandDataTransformerInterface[] */
    private $commandDataTransformers;

    public function __construct(CommandDataTransformerInterface ...$commandDataTransformers)
    {
        $this->commandDataTransformers = $commandDataTransformers;
    }

    public function transform($object, string $to, array $context = [])
    {
        foreach ($this->commandDataTransformers as $transformer) {
            if ($transformer->supportsTransformation($object)) {
                $object = $transformer->transform($object, $to, $context);
            }
        }

        return $object;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return
            isset($context['input']['class']) &&
            is_a($context['input']['class'], CommandAwareDataTransformerInterface::class, true)
        ;
    }
}
