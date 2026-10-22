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

namespace Sylius\Bundle\ApiBundle\Validator;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\DenormalizationViolationFactoryInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;

/**
 * API Platform 5 supports only array validation groups here, not validation groups generators.
 *
 * @internal
 */
final readonly class DenormalizationViolationFactory implements DenormalizationViolationFactoryInterface
{
    public function __construct(private DenormalizationViolationFactoryInterface $decoratedFactory)
    {
    }

    public function handle(NotNormalizableValueException|PartialDenormalizationException $exception, Operation $operation): void
    {
        $validationContext = $operation->getValidationContext() ?? [];

        if (isset($validationContext['groups']) && !is_array($validationContext['groups'])) {
            unset($validationContext['groups']);
            $operation = $operation->withValidationContext($validationContext);
        }

        $this->decoratedFactory->handle($exception, $operation);
    }
}
