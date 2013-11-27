<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Model\VariantImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    protected $dataClass;
    protected $images = array();

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * {@docinherit}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $image = current($this->images);

        if (false !== $image && $image instanceof VariantImage) {
            $view->children['file']->vars['image_path'] = $image->getPath();
        }

        next($this->images);
    }

    /**
     * {@docinherit}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array(
            'label' => false
        ));

        $that = &$this;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($that) {
            $this->images[] = $event->getData();
        });
    }

    /**
     * {@docinherit}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
        ;
    }

    /**
     * {@docinherit}
     */
    public function getName()
    {
        return 'sylius_image';
    }
}
