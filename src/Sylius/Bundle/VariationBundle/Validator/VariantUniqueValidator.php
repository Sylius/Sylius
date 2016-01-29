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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Unique variant constraint validator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantUniqueValidator extends ConstraintValidator
{
    /**
     * @var RepositoryInterface
     */
    protected $variantRepository;

    /**
     * @param RepositoryInterface $variantRepository
     */
    public function __construct(RepositoryInterface $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof VariantInterface) {
            throw new UnexpectedTypeException($value, VariantInterface::class);
        }

        $variant = $value;
        $accessor = PropertyAccess::createPropertyAccessor();

        $conflictualVariant = $this->variantRepository->findOneBy([$constraint->property => $accessor->getValue($variant, $constraint->property)]);

        if (null !== $conflictualVariant && $conflictualVariant !== $variant) {
            $this->context->addViolationAt($constraint->property, $constraint->message, [
                '%property%' => $constraint->property,
            ]);
        }
    }
}
