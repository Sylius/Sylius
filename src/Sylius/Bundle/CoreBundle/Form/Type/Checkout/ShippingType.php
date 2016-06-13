<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ShippingType extends AbstractType
{
    /**
     * @var ZoneMatcherInterface
     */
    private $zoneMatcher;

    /**
     * @param ZoneMatcherInterface $zoneMatcher
     */
    public function __construct(ZoneMatcherInterface $zoneMatcher)
    {
        $this->zoneMatcher = $zoneMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('shipments', 'collection', [
            'type' => 'sylius_checkout_shipment',
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shop_checkout_shipping';
    }
}
