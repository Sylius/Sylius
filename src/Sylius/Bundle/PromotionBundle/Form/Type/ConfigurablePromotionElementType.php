<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Component\Promotion\Model\ConfigurablePromotionElementInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ConfigurablePromotionElementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $data = $event->getData();

                $type = null;
                if ($data instanceof ConfigurablePromotionElementInterface) {
                    $type = $data->getType();
                }

                if (null === $data) {
                    $type = $options['configuration_type'];
                }

                if (null === $type) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['type'])) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $data['type']);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('configuration_type', null)
            ->setAllowedTypes('configuration_type', ['string', 'null'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_configurable_promotion_element';
    }

    /**
     * @param FormInterface $form
     * @param string $configurationType
     */
    private function addConfigurationFields(FormInterface $form, $configurationType)
    {
        $form->add('configuration', $configurationType, [
            'label' => false,
        ]);
    }
}
