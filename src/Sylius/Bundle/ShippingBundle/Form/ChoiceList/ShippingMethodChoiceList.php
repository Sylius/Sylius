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
use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

/**
 * Choice List which enhances Choice Views
 */
class ShippingMethodChoiceList extends LazyChoiceList
{
    protected $methods;
    protected $shippingSubject;
    protected $enhancer;

    public function __construct(ChoiceList $methods, ShippingSubjectInterface $shippingSubject, ShippingMethodChoiceViewEnhancerInterface $enhancer)
    {
        $this->methods = $methods;
        $this->shippingSubject = $shippingSubject;
        $this->enhancer = $enhancer;
    }

    protected function enhanceViews($views)
    {
        $enhancedViews = array();

        foreach ($views as $view) {
            $enhancedViews[] = $this->enhancer->enhanceChoiceView($view, $this->shippingSubject);
        }

        return $enhancedViews;
    }

    public function getRemainingViews()
    {
        $views = parent::getRemainingViews();

        return $this->enhanceViews($views);
    }

    public function getPreferredViews()
    {
        $views = parent::getPreferredViews();

        return $this->enhanceViews($views);
    }

    /**
     * Loads the choice list
     *
     * Should be implemented by child classes.
     *
     * @return ChoiceListInterface The loaded choice list
     */
    protected function loadChoiceList()
    {
        return $this->methods;
    }
}