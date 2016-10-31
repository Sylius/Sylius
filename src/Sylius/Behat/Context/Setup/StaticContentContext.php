<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ContentBundle\Document\StaticContent;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class StaticContentContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ExampleFactoryInterface
     */
    private $staticContentExampleFactory;

    /**
     * @var ObjectManager
     */
    private $staticContentManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ExampleFactoryInterface $staticContentExampleFactory
     * @param ObjectManager $staticContentManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ExampleFactoryInterface $staticContentExampleFactory,
        ObjectManager $staticContentManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->staticContentExampleFactory = $staticContentExampleFactory;
        $this->staticContentManager = $staticContentManager;
    }

    /**
     * @Given the store has static content :title
     */
    public function theStoreHasStaticContent($title)
    {
        $staticContent = $this->staticContentExampleFactory->create(['title' => $title]);

        $this->staticContentManager->persist($staticContent);
        $this->staticContentManager->flush();

        $this->sharedStorage->set('static_content', $staticContent);
    }

    /**
     * @Given the store has static contents :firstTitle and :secondTitle
     */
    public function theStoreHasStaticContents(...$titles)
    {
        foreach ($titles as $title) {
            $this->theStoreHasStaticContent($title);
        }
    }

    /**
     * @Given the store has static content :title with body :body
     */
    public function theStoreHasStaticContentWithBody($title, $body)
    {
        $staticContent = $this->staticContentExampleFactory->create(['title' => $title, 'body' => $body]);

        $this->staticContentManager->persist($staticContent);
        $this->staticContentManager->flush();

        $this->sharedStorage->set('static_content', $staticContent);
    }

    /**
     * @Given the store has static content :title with name :name
     */
    public function theStoreHasStaticContentWithName($title, $name)
    {
        $staticContent = $this->staticContentExampleFactory->create(['title' => $title, 'name' => $name]);

        $this->staticContentManager->persist($staticContent);
        $this->staticContentManager->flush();

        $this->sharedStorage->set('static_content', $staticContent);
    }

    /**
     * @Given /^(it) is not published yet$/
     */
    public function itIsNotPublishedYet(StaticContent $staticContent)
    {
        $staticContent->setPublishable(false);

        $this->staticContentManager->flush();
    }
}
