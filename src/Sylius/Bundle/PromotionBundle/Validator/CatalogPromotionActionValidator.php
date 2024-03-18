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

namespace Sylius\Bundle\PromotionBundle\Validator;

use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

trigger_deprecation(
    'sylius/promotion-bundle',
    '1.13',
    'The "%s" class is deprecated and will be removed in Sylius 2.0, use the usual symfony logic for validation.',
    CatalogPromotionActionValidator::class,
);
final class CatalogPromotionActionValidator extends ConstraintValidator
{
    private array $actionValidators;

    public function __construct(private array $actionTypes, iterable $actionValidators)
    {
        $this->actionValidators = $actionValidators instanceof \Traversable ? iterator_to_array($actionValidators) : $actionValidators;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var CatalogPromotionAction $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionAction::class);

        $type = $value->getType();
        if (!array_key_exists($type, $this->actionValidators)) {
            return;
        }

        $configuration = $value->getConfiguration();

        /** @var ActionValidatorInterface $validator */
        $validator = $this->actionValidators[$type];
        $validator->validate($configuration, $constraint, $this->context);
    }
}
