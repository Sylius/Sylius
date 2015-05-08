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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Symfony\Component\Form\FormBuilderInterface;

class AffiliateType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', array(
                'label' => 'sylius.form.affiliate.status',
                'choices' => array(
                    AffiliateInterface::AFFILIATE_ENABLED  => 'sylius.form.affiliate.enabled',
                    AffiliateInterface::AFFILIATE_PAUSED   => 'sylius.form.affiliate.paused',
                    AffiliateInterface::AFFILIATE_DISABLED => 'sylius.form.affiliate.disabled',
                ),
            ))
            ->add('referralCode', 'text', array(
                'label' => 'sylius.form.affiliate.referral_code',
                'read_only' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_affiliate';
    }
}
