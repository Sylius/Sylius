<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Form\Type\Configuration;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class HiddenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sylius_cache', HiddenType::class, array(
                'data' => 'file_system',
                'constraints' => array(
                    new Assert\NotBlank(),
                )
            ))
            ->add('sylius_secret', HiddenType::class, array(
                'data' => uniqid(),
                'constraints' => array(
                    new Assert\NotBlank(),
                )
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'sylius_configuration_hidden';
    }
}
