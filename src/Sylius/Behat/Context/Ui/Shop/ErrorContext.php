<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Sylius\Behat\Page\Shop\Error\ErrorPage;
use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class ErrorContext implements Context
{
    /**
     * @var ErrorPage
     */
    private $errorPage;

    /**
     * @param ErrorPage $errorPage
     */
    public function __construct(ErrorPage $errorPage)
    {
        $this->errorPage = $errorPage;
    }

    /**
     * @When I am on not found page
     */
    public function iAmOnNotFoundPage()
    {
        $this->errorPage->open(['code' => Response::HTTP_NOT_FOUND]);
    }

    /**
     * @When I am on forbidden page
     */
    public function iAmOnForbiddenPage()
    {
        $this->errorPage->open(['code' => Response::HTTP_FORBIDDEN]);
    }

    /**
     * @Then I should see the title :title
     */
    public function iShouldSeeTheTitle(string $title)
    {
        Assert::eq($title, $this->errorPage->getTitle());
    }

    /**
     * @Then I should be informed that the page does not exist
     */
    public function iShouldBeInformedThatThePageDoesNotExist()
    {
        Assert::eq("The page you are looking for does not exist.", $this->errorPage->getTitle());
    }

    /**
     * @Then I should be informed that the page is forbidden
     */
    public function iShouldBeInformedThatThePageIsForbidden()
    {
        Assert::eq("The page you are looking for is forbidden.", $this->errorPage->getTitle());
    }
}
