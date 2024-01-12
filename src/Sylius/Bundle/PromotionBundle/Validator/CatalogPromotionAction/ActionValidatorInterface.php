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

namespace Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

trigger_deprecation(
    'sylius/promotion-bundle',
    '1.13',
    'The "%s" interface is deprecated and will be removed in Sylius 2.0, use the usual symfony logic for validation.',
    ActionValidatorInterface::class,
);
interface ActionValidatorInterface
{
    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void;
}
