<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Twig;

use Sylius\Bundle\OrderBundle\Templating\Helper\CartHelper;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartExtension extends \Twig_Extension
{
    /**
     * @var CartHelper
     */
    private $helper;

    /**
     * @param CartHelper $helper
     */
    public function __construct(CartHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
             new \Twig_SimpleFunction('sylius_cart_exists', [$this, 'hasCart']),
             new \Twig_SimpleFunction('sylius_cart_get', [$this, 'getCurrentCart']),
             new \Twig_SimpleFunction('sylius_cart_form', [$this, 'getItemFormView']),
        ];
    }

    /**
     * @return null|OrderInterface
     */
    public function getCurrentCart()
    {
        return $this->helper->getCurrentCart();
    }

    /**
     * @return bool
     */
    public function hasCart()
    {
        return $this->helper->hasCart();
    }

    /**
     * @param array $options
     *
     * @return FormView
     */
    public function getItemFormView(array $options = [])
    {
        return $this->helper->getItemFormView($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_cart';
    }
}
