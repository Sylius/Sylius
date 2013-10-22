<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\View;

use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * Applies enhancements to ChoiceView instances
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface ShippingMethodChoiceViewEnhancerInterface
{
    /**
     * Enhances the passed choice view according to the passed shipping subject
     *
     * @param ChoiceView $view
     * @param ShippingSubjectInterface $subject
     * @return mixed
     */
    function enhanceChoiceView(ChoiceView $view, ShippingSubjectInterface $subject);
}