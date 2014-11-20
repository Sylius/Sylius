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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Sylius\Component\Core\Model\SubscriptionInterface;

class SubscribableCartItemType extends CartItemType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('subscription', 'sylius_simple_subscription', array(
            'label' => 'sylius.form.subscription.label'
        ));

        // remove subscription if fields not filled in form
        $builder->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $item = $event->getData();

                /** @var SubscriptionInterface $subscription */
                $subscription = $item->getSubscription();

                if (null === $subscription || null === $subscription->getInterval()) {
                    $item->setSubscription(null);
                }
            }
        );
    }
}
