<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Base province choice type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ProvinceChoiceType extends AbstractType
{
    /**
     * Province repository.
     *
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $repository = $this->repository;

        $choiceList = function (Options $options) use ($repository) {
            if (null === $options['country']) {
                return new ObjectChoiceList($repository->findAll());
            }

            return new ObjectChoiceList($options['country']->getProvinces());
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList,
                'country'     => null,
                'label'   => 'sylius.form.address.province',
                'empty_value' => 'sylius.form.province.select'
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
        return 'sylius_province_choice';
    }
}
