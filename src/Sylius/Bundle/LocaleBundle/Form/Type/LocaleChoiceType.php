<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourcesToIdentifiersTransformer;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LocaleChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $localeRepository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->localeRepository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (true === $options['multiple']) {
            $builder->addModelTransformer(new ResourcesToIdentifiersTransformer($this->localeRepository, 'code'));
            //$builder->addViewTransformer(new CollectionToArrayTransformer(), true);
        } else {
            $builder->addModelTransformer(new ResourceToIdentifierTransformer($this->localeRepository, 'code'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choiceList = function (Options $options) {
            if (null === $options['enabled']) {
                $choices = $this->localeRepository->findAll();
            } else {
                $choices = $this->localeRepository->findBy(['enabled' => $options['enabled']]);
            }

            return new ObjectChoiceList($choices);
        };

        $resolver
            ->setDefaults([
                'choice_translation_domain' => false,
                'choice_list' => $choiceList,
                'enabled' => null,
                'label' => 'sylius.form.locale.locale',
                'empty_value' => 'sylius.form.locale.select',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale_choice';
    }
}
