<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Validator;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ProductBundle\Model\ProductInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Unique product constraint validator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductUniqueValidator extends ConstraintValidator
{
    /**
     * Product manager.
     *
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ProductInterface) {
            throw new UnexpectedTypeException($value, 'Sylius\Bundle\ProductBundle\Model\ProductInterface');
        }

        $product = $value;
        $accessor = PropertyAccess::createPropertyAccessor();

        $criteria = array($constraint->property => $accessor->getValue($product, $constraint->property));
        $conflictualProduct = $this->repository->findOneBy($criteria);

        if (null !== $conflictualProduct && $conflictualProduct != $product) {
            $this->context->addViolationAt($constraint->property, $constraint->message, array(
                '%property%' => $constraint->property
            ));
        }
    }
}
