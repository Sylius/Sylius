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

use Symfony\Component\Validator\Constraints\Choice as BaseChoice;

/**
* @author Patrick McDougle <patrick@patrickmcdougle.com>
*/
class Choice extends BaseChoice
{
    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return get_class().'Validator';
    }
}
