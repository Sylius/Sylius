<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CurrentPageResolver implements CurrentPageResolverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var UrlMatcherInterface
     */
    private $urlMatcher;

    /**
     * @param Session $session
     * @param UrlMatcherInterface $urlMatcher
     */
    public function __construct(Session $session, UrlMatcherInterface $urlMatcher)
    {
        $this->session = $session;
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * {@inheritdoc}
     * 
     * @throws \LogicException
     */
    public function getCurrentPageWithForm(CreatePageInterface $createPage, UpdatePageInterface $updatePage)
    {
        $routeParameters = $this->urlMatcher->match(parse_url($this->session->getCurrentUrl(), PHP_URL_PATH));

        if (false !== strpos($routeParameters['_route'], 'create')) {
            return $createPage;
        }

        if (false !== strpos($routeParameters['_route'], 'update')) {
            return $updatePage;
        }

        throw new \LogicException('Route name does not have any of "update" or "create" keyword, so matcher was unable to match proper page.');
    }
}
