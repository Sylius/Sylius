<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectToIdentifierTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Object to identifier type.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ObjectToIdentifierType extends AbstractType
{
    /**
     * Manager registry.
     *
     * @var ManagerRegistry
     */
    protected $manager;

    /**
     * Form name.
     *
     * @var string
     */
    protected $name;

    public function __construct(ManagerRegistry $manager, $name)
    {
        $this->manager = $manager;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new ObjectToIdentifierTransformer($this->manager->getRepository($options['class']), $options['identifier'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'identifier' => 'id',
            ))
            ->setAllowedTypes(array(
                'identifier' => array('string'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
