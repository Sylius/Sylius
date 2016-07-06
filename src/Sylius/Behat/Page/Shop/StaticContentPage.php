<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop;

use Sylius\Behat\Page\SymfonyPage;
use Sylius\Bundle\ContentBundle\Document\StaticContent;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class StaticContentPage extends SymfonyPage implements StaticContentPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function access($path)
    {
        return $this->getDriver()->visit($this->makePathAbsolute($path));
    }

    /**
     * {@inheritdoc}
     */
    public function assertPageHasContent(StaticContent $staticContent)
    {
        $this->verify(['name' => $staticContent->getName()]);

        Assert::contains($this->getSession()->getPage()->getHtml(), $staticContent->getTitle());
        Assert::contains($this->getSession()->getPage()->getHtml(), $staticContent->getBody());
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_static_content_show';
    }
}
