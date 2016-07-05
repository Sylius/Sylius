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
    public function theStoreHasStaticContents($firstTitle, $secondTitle)
    {
        $this->theStoreHasStaticContent($firstTitle);
        $this->theStoreHasStaticContent($secondTitle);
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
}
