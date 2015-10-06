<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule\Affiliate;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Contains affiliate Uri visit rule configuration form type.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class RegistrationConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_affiliate_rule_registration_configuration';
    }
}