<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\ReportBundle\DataFetcher\UserRegistrationDataFetcher;

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
                'label' => 'sylius.form.report.user_registration.start'
            ))
            ->add('end', 'date', array(
                'label' => 'sylius.form.report.user_registration.end'
            ))
            ->add('period', 'choice', array(
                'choices'  => UserRegistrationDataFetcher::getPeriodChoices(),
                'multiple' => false,
                'label' => 'sylius.form.report.user_registration.period'
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