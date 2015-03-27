<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Twig;

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Geoffrey Brier <geoffrey.brier@gmail.com>
 */
class SecurityExtension extends \Twig_Extension
{
    /** @var CsrfProviderInterface */
    protected $csrfProvider;

    /** @var string */
    protected $intention;

    /**
     * Constructor.
     *
     * @param CsrfProviderInterface $csrfProvider
     * @param string                $intention
     */
    public function __construct(CsrfProviderInterface $csrfProvider, $intention)
    {
        $this->csrfProvider = $csrfProvider;
        $this->intention = $intention;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
             new \Twig_SimpleFunction(
                 'sylius_generate_csrf_token',
                 array($this, 'generateCsrfToken')
             ),
        );
    }

    /**
     * @return string
     */
    public function generateCsrfToken()
    {
        return $this->csrfProvider->generateCsrfToken($this->intention);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_security';
    }
}
