<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Richtermeister
 * Date: 9/27/13
 * Time: 5:19 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Sylius\Bundle\ShippingBundle\Form\ChoiceList;


use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

/**
 * Creates a choice list to select a shipping method
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface ShippingMethodChoiceListFactoryInterface
{
    /**
     * Returns choice list based on passed shipping subject and methods
     *
     * @param ShippingSubjectInterface $subject
     * @param ShippingMethodInterface[] $methods
     * @return ChoiceList
     */
    function createChoiceList(ShippingSubjectInterface $subject, $methods);
}