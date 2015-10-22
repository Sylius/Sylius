<?php

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule\Affiliate;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Affiliate\Repository\AffiliateRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ReferrerConfigurationType extends AbstractType
{
    /**
     * @var AffiliateRepositoryInterface
     */
    protected $affiliateRepository;
    protected $validationGroups;

    public function __construct(EntityRepository $affiliateRepository, array $validationGroups)
    {
        $this->affiliateRepository = $affiliateRepository;
        $this->validationGroups     = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $affiliateRepository = $this->affiliateRepository;

        $builder
            ->add('affiliate', 'sylius_entity_to_identifier', array(
                'label'         => 'sylius.form.rule.referrer_configuration.affiliate',
                'class'         => $affiliateRepository->getClassName(),
                'query_builder' => function () use ($affiliateRepository) {
                    return $affiliateRepository->getFormQueryBuilder();
                },
                'choice_label' => 'customer.user.username',
                'constraints'   => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_affiliate_rule_referrer_configuration';
    }
}