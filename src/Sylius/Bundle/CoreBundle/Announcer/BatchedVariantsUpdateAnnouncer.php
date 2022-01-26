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

namespace Sylius\Bundle\CoreBundle\Announcer;

use Sylius\Component\Product\Command\UpdateBatchedVariants;
use Symfony\Component\Messenger\MessageBusInterface;

final class BatchedVariantsUpdateAnnouncer implements BatchedVariantsUpdateAnnouncerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private int $size
    ) {
    }

    public function dispatchVariantsUpdateCommand(array $variants): void
    {
        $batchedVariants = array_chunk($variants, $this->size);

        foreach ($batchedVariants as $batch) {
            $this->messageBus->dispatch(new UpdateBatchedVariants($batch));
        }
    }
}
