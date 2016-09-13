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
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
* @author Patrick McDougle <patrick@patrickmcdougle.com>
*/
class ChoiceValidator extends BaseChoiceValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Choice) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Choice');
        }

        if (!is_array($constraint->choices) && !$constraint->callback) {
            throw new ConstraintDefinitionException('Either "choices" or "callback" must be specified on constraint Choice');
        }

        if (null === $value) {
            return;
        }

        if ($constraint->callback) {
            $object = $this->context->getObject();
            if (isset($object)
                && !is_callable($choices = array($object, $constraint->callback))
                && !is_callable($choices = $constraint->callback)
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
