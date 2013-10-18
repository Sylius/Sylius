<?php


namespace Sylius\Bundle\ReviewBundle\Form\Type;


use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\ReviewBundle\Model\ReviewInterface;

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