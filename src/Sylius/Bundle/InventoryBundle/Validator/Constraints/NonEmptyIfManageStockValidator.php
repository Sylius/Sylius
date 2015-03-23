<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 *
 * @author Myke Hines <myke@webhines.com>
 */
class NonEmptyIfManageStockValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
    	$fields = $constraint->fields;

        if ($entity->isManageStock())
        {
        	foreach ($fields as $field)
        	{
                $value = call_user_func(array($entity, 'get' . $field));
	        	if (empty($value))
	    			$this->context->addViolationAt($field, $constraint->message);
	    	}
        }

    }
}
