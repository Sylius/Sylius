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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\ApplyCatalogPromotionsOnVariants;
use Symfony\Component\Messenger\MessageBusInterface;

final class BatchedApplyCatalogPromotionsOnVariantsCommandDispatcher implements ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface
{
    /** @var positive-int */
    private int $size;

    public function __construct(
        private MessageBusInterface $messageBus,
        int $size,
    ) {
        if ($size < 1) {
            throw new \InvalidArgumentException('Size must be greater than 0');
        }
        $this->size = $size;
    }

    public function updateVariants(array $variantsCodes): void
    {
        $batchedVariants = array_chunk($variantsCodes, $this->size);

        foreach ($batchedVariants as $batch) {
            $this->messageBus->dispatch(new ApplyCatalogPromotionsOnVariants($batch));
        }
    }
}
