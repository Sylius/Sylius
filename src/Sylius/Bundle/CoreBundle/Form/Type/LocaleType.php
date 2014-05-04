<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Locale type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LocaleType extends AbstractType
{
    private $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'locale', array(
                'label'       => 'sylius.form.locale.code',
                'empty_value' => 'sylius.form.locale.select_code',
            ))
            ->add('currency', 'currency', array(
                'label'       => 'sylius.form.locale.currency',
                'empty_value' => 'sylius.form.locale.select_currency',
            ))
            ->add('enabled', 'checkbox', array(
                'label'    => 'sylius.form.locale.enabled',
                'required' => false,
            ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => $this->dataClass,
                'validation_groups' => array('sylius')
            )
        );
    }

    public function getName()
    {
        return 'sylius_locale';
    }
}
