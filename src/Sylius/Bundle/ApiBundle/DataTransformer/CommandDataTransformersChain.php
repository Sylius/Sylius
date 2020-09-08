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

/** @experimental */
final class CommandDataTransformersChain implements CommandDataTransformersChainInterface
{
    /** @var CommandDataTransformerInterface[] */
    private $commandDataTransformers;

    public function __construct(CommandDataTransformerInterface ...$commandDataTransformers)
    {
        $this->commandDataTransformers = $commandDataTransformers;
    }

    /**
     * @return CommandDataTransformerInterface[]
     */
    public function getCommandDataTransformers(): array
    {
        return $this->commandDataTransformers;
    }
}
