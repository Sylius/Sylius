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

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\StaticContentPageInterface;
use Sylius\Bundle\ContentBundle\Document\StaticContent;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class StaticContentContext implements Context
{
    /**
     * @var StaticContentPageInterface
     */
    private $staticContentPage;

    /**
     * @param StaticContentPageInterface $staticContentPage
     */
    public function __construct(StaticContentPageInterface $staticContentPage)
    {
        $this->staticContentPage = $staticContentPage;
    }

    /**
     * @When I access static content with name :name
     */
    public function iAccessStaticContentWithName($name)
    {
        $this->staticContentPage->tryToOpen(['name' => $name]);
    }

    /**
     * @Then /^I should see (that static content)$/
     */
    public function iShouldSeeThatStaticContent(StaticContent $staticContent)
    {
        $this->staticContentPage->assertPageHasContent($staticContent);
    }
}
