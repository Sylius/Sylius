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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Input;

use ApiPlatform\Metadata\Operation;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/** @experimental */
final readonly class LocaleCodeAwareInputDataProcessor implements InputDataProcessorInterface
{
    public function __construct(private LocaleContextInterface $localeContext)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data->getLocaleCode() !== null) {
            return [$data, $operation, $uriVariables, $context];
        }

        $localeCode = $this->localeContext->getLocaleCode();

        $data->setLocaleCode($localeCode);

        return [$data, $operation, $uriVariables, $context];
    }

    public function supports(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): bool
    {
        return $data instanceof LocaleCodeAwareInterface;
    }
}
