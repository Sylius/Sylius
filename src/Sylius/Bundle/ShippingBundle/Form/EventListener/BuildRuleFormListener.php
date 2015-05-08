<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\EventListener;

use Sylius\Bundle\ResourceBundle\Form\EventListener\BuildRuleFormSubscriber as BaseBuildRuleFormSubscriber;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * This listener adds configuration form to a rule,
 * if selected rule requires one.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class BuildRuleFormListener extends BaseBuildRuleFormSubscriber
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preBind'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function addConfigurationFields(FormInterface $form, $ruleType, array $data = array())
    {
        $model = $this->registry->get($ruleType);

        $form->add($this->factory->createNamed(
            'configuration',
            $model->getConfigurationFormType(),
            $data,
            array(
                'auto_initialize' => false,
            )
        ));
    }
}
