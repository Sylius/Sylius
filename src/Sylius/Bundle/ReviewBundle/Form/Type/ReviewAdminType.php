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

use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewAdminType extends ReviewType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('rating');
        $builder->add('status', 'choice', array(
            'choices' => array(
                ReviewInterface::MODERATION_STATUS_NEW => 'sylius.form.review.status.new',
                ReviewInterface::MODERATION_STATUS_APPROVED => 'sylius.form.review.status.approved',
                ReviewInterface::MODERATION_STATUS_REJECTED => 'sylius.form.review.status.rejected'
            ),
            'label' => 'sylius.form.review.status.label'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_review_admin';
    }
}
