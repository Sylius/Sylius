<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\AffiliateBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\ImageType;
use Symfony\Component\Form\FormBuilderInterface;

class BannerType extends ImageType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.banner.name'
            ))
            ->add('status', 'choice', array(
                'label' => 'sylius.form.banner.enabled',
                'choices' => array(
                    0 => 'sylius.no',
                    1 => 'sylius.yes',
                ),
            ))
        ;

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_affiliate_banner';
    }
}
