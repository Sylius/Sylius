<?php

namespace Sylius\Bundle\ResourceBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class Enabled extends Constraint
{
    public $message = 'sylius.resource.not_enabled';

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
        return 'sylius_resource_enabled_validator';
    }
}