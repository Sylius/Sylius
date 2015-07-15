<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\DataFetcher;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\CoreBundle\DataFetcher\UserRegistrationDataFetcher;

/**
 * User based raport configuration form type.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserRegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', 'date', array(
                'label' => 'sylius.form.report.user_registration.start',
            ))
            ->add('end', 'date', array(
                'label' => 'sylius.form.report.user_registration.end',
            ))
            ->add('period', 'choice', array(
                'choices'  => UserRegistrationDataFetcher::getPeriodChoices(),
                'multiple' => false,
                'label' => 'sylius.form.report.user_registration.period',
            ))
            ->add('empty_records', 'checkbox', array(
                'label'    => 'sylius.form.report.user_registration.empty_records',
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_data_fetcher_user_registration';
    }
}
