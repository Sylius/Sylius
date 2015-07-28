<?php

namespace Sylius\Bundle\ResourceBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class Disabled extends Constraint
{
    public $message = 'sylius.resource.not_disabled';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return array(self::PROPERTY_CONSTRAINT, self::CLASS_CONSTRAINT);
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_resource_disabled_validator';
    }
}