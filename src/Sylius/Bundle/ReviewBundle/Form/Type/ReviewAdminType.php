<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\ReviewBundle\Model\ReviewInterface;

/**
 * ReviewAdminType
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ReviewAdminType extends ReviewType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('rating');
        $builder->add('moderationStatus', 'choice', array(
            'choices' => array(
                ReviewInterface::MODERATION_STATUS_UNMODERATED => 'Unmoderated',
                ReviewInterface::MODERATION_STATUS_APPROVED => 'Approved',
                ReviewInterface::MODERATION_STATUS_REJECTED => 'Rejected'
            )
        ));
    }

    public function getName()
    {
        return 'sylius_review_admin';
    }
}
