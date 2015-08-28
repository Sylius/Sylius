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

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LocaleChoiceType extends AbstractType
{
    /**
     * @var ObjectRepository
     */
    protected $localeRepository;

    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->localeRepository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addViewTransformer(new CollectionToArrayTransformer(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $localeRepository = $this->localeRepository;

        $choiceList = function (Options $options) use ($localeRepository) {
            if (null === $options['enabled']) {
                $choices = $localeRepository->findAll();
            } else {
                $choices = $localeRepository->findBy(array('enabled' => $options['enabled']));
            }

            return new ObjectChoiceList($choices, null, array(), null, 'id');
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList,
                'enabled'     => null,
                'label'       => 'sylius.form.locale.locale',
                'empty_value' => 'sylius.form.locale.select',
            ))
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
