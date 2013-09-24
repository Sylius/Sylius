<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Validator;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\VariableProductBundle\Model\VariantInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Unique product variant constraint validator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantUniqueValidator extends ConstraintValidator
{
    /**
     * Variant manager.
     *
     * @var ObjectRepository
     */
    protected $variantRepository;

    /**
     * Constructor.
     *
     * @param ObjectRepository $variantRepository
     */
    public function __construct(ObjectRepository $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof VariantInterface) {
            throw new UnexpectedTypeException($value, 'Sylius\Bundle\VariableProductBundle\Model\VariantInterface');
        }

        $variant = $value;
        $accessor = PropertyAccess::createPropertyAccessor();

        $criteria = array($constraint->property => $accessor->getValue($variant, $constraint->property));
        $conflictualVariant = $this->variantRepository->findOneBy($criteria);

        if (null !== $conflictualVariant && $conflictualVariant !== $variant) {
            $this->context->addViolationAt($constraint->property, $constraint->message, array(
                '%property%' => $constraint->property
            ));
        }
    }
}
