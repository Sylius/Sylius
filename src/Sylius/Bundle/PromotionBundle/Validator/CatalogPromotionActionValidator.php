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
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CatalogPromotionActionValidator extends ConstraintValidator
{
    private array $actionValidators;

    public function __construct(private array $actionTypes, iterable $actionValidators)
    {
        $this->actionValidators = $actionValidators instanceof \Traversable ? iterator_to_array($actionValidators) : $actionValidators;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var CatalogPromotionAction $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionAction::class);

        /** @var CatalogPromotionActionInterface $value */
        if (!in_array($value->getType(), $this->actionTypes, true)) {
            $this->context->buildViolation($constraint->invalidType)->atPath('type')->addViolation();

            return;
        }

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
