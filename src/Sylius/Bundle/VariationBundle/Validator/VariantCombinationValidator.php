<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Validator;

use Sylius\Component\Variation\Checker\VariantsParityCheckerInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantCombinationValidator extends ConstraintValidator
{
    /**
     * @var VariantsParityCheckerInterface
     */
    private $variantsParityChecker;

    /**
     * @param VariantsParityCheckerInterface $variantsParityChecker
     */
    public function __construct(VariantsParityCheckerInterface $variantsParityChecker)
    {
        $this->variantsParityChecker = $variantsParityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof VariantInterface) {
            throw new UnexpectedTypeException($value, VariantInterface::class);
        }

        $variable = $value->getObject();
        if (!$variable->hasVariants() || !$variable->hasOptions()) {
            return;
        }

        if ($this->variantsParityChecker->checkParity($value, $variable)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
