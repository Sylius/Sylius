<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu;

use JMS\TranslationBundle\Annotation\Ignore;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Abstract menu builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class MenuBuilder
{
    /**
     * Menu factory.
     *
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Security context.
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * Translator instance.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Request.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     *
     * @param FactoryInterface         $factory
     * @param SecurityContextInterface $securityContext
     * @param TranslatorInterface      $translator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        FactoryInterface $factory,
        SecurityContextInterface $securityContext,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->factory = $factory;
        $this->securityContext = $securityContext;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Sets the request the service
     *
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Translate label.
     *
     * @param string $label
     * @param array  $parameters
     *
     * @return string
     */
    protected function translate($label, $parameters = array())
    {
        return $this->translator->trans(/** @Ignore */ $label, $parameters, 'menu');
    }
}
