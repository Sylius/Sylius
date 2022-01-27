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

namespace Sylius\Bundle\CoreBundle\Commander;

use Sylius\Component\Product\Command\UpdateVariants;
use Symfony\Component\Messenger\MessageBusInterface;

final class UpdateVariantsCommander implements UpdateVariantsCommanderInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private int $size
    ) {
    }

    public function updateVariants(array $variants): void
    {
        $batchedVariants = array_chunk($variants, $this->size);

        foreach ($batchedVariants as $batch) {
            $this->messageBus->dispatch(new UpdateVariants($batch));
        }
    }
}
