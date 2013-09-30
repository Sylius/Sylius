<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\ChoiceList;

use Sylius\Bundle\ShippingBundle\Form\View\ShippingMethodChoiceViewEnhancerInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;
use Sylius\Bundle\ShippingBundle\Sorter\ShippingMethodSorterInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

/**
 * Creates a shipping choice list that is sorted and enhanced with extra view data
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ShippingMethodChoiceListFactory implements ShippingMethodChoiceListFactoryInterface
{
    protected $enhancer;
    protected $sorter;

    public function __construct(ShippingMethodChoiceViewEnhancerInterface $enhancer, ShippingMethodSorterInterface $sorter = null)
    {
        $this->enhancer = $enhancer;
        $this->sorter = $sorter;
    }

    /**
     * @param ShippingSubjectInterface $subject
     * @param \Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface[] $methods
     * @return ShippingMethodChoiceList|\Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList
     */
    function createChoiceList(ShippingSubjectInterface $subject, $methods)
    {
        if ($this->sorter) {
            $methods = $this->sorter->sort($methods, $subject);
        }

        return new ShippingMethodChoiceList(new ObjectChoiceList($methods), $subject, $this->enhancer);
    }
}