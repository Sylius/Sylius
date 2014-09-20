<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Job type form
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobType extends AbstractType
{
    /**
     * Job model class.
     *
     * @var string
     */
    protected $className;

    /**
     * Constructor.
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('command')
            ->add('description')
            ->add('schedule')
            ->add('environment')
            ->add('serverType')
            ->add('priority', 'integer', array(
                'required' => false,
            ))
            ->add('active', 'checkbox', array(
                'required' => false,
            ));
    }

    /**
     * @return string The name of this type
     */
    function getName()
    {
        return 'sylius_job';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->className,
        ));
    }
}