<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator as BaseChoiceValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
* @author Patrick McDougle <patrick@patrickmcdougle.com>
*/
class ChoiceValidator extends BaseChoiceValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Choice) {
            throw new UnexpectedTypeException($constraint, Choice::class);
        }

        if (null === $value) {
            return;
        }

        if ($this->context instanceof ExecutionContextInterface && $constraint->callback) {
            $object = $this->context->getObject();
            if (!isset($object)
                || !is_callable($choices = [$object, $constraint->callback])
            ) {
                throw new ConstraintDefinitionException('The Choice constraint expects a valid callback');
            }
            $choices = call_user_func($choices);
            if ($choices instanceof Collection) {
                $choices = $choices->toArray();
            }
            $constraint->choices = $choices;
            $constraint->callback = null;
        }

        return parent::validate($value, $constraint);
    }
}
