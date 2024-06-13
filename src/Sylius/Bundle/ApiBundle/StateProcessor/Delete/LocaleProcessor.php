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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Delete;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Bundle\ApiBundle\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<LocaleInterface> */
final readonly class LocaleProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $removeProcessor,
        private LocaleUsageCheckerInterface $localeUsageChecker,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, LocaleInterface::class);
        Assert::isInstanceOf($operation, DeleteOperationInterface::class);

        if ($this->localeUsageChecker->isUsed($data->getCode())) {
            throw new LocaleIsUsedException($data->getCode());
        }

        return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
    }
}
