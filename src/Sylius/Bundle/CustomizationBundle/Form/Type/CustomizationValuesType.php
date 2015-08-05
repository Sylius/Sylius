<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CustomizationBundle\Form\Type;

use Gedmo\Sluggable\Util\Urlizer;
use Sylius\Component\Customization\Model\CustomizationInterface;
use Sylius\Component\Customization\Model\CustomizationSubjectInstanceInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomizationValuesType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $customizationValueRepository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->customizationValueRepository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repository = $this->customizationValueRepository;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use($options, $repository) {
            /** @var CustomizationSubjectInstanceInterface $subjectInstance */
            $subjectInstance = $options['subjectInstance'];

            foreach ($options['customizations'] as $i => $customization) {
                if (!$customization instanceof CustomizationInterface) {
                    throw new UnexpectedTypeException($customization, 'Sylius\Component\Customization\Model\CustomizationInterface');
                }

                $customizationValue = $subjectInstance->getCustomizationValueByName($customization->getName());

                if (null === $customizationValue) {
                    $customizationValue = $repository->createNew();
                    $customizationValue->setCustomization($customization);
                    $subjectInstance->addCustomizationValue($customizationValue);
                }

                $event->getForm()->add(Urlizer::urlize($customization->getName()), 'sylius_customization_value', array(
                    'customization' => $customization,
                    'label'         => false,
                    'data'          => $customizationValue,
                    'property_path' => '['.$i.']'
                ));
            }
        });

    }

    /**
     * (@inheritdoc)
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setRequired(array(
                'subjectInstance',
                'customizations'
            ))
            ->setAllowedTypes(array(
                'subjectInstance' => array('Sylius\Component\Customization\Model\CustomizationSubjectInstanceInterface'),
                'customizations'  => array('array', 'Doctrine\Common\Collections\Collection')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_customization_values';
    }
}
