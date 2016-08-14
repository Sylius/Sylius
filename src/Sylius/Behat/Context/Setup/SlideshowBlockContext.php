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
use Sylius\Bundle\ContentBundle\Document\Slideshow;
use Sylius\Bundle\ContentBundle\Document\SlideshowBlock;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;

/**
 * @author Vidy Videni <vidy.videni@gmail.com>
 */
final class SlideshowBlockContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ExampleFactoryInterface
     */
    private $slideshowBlockExampleFactory;

    /**
     * @var ObjectManager
     */
    private $slideshowBlockManager;

    private $imagineBlockExampleFactory;

    /**
     * @param SharedStorageInterface  $sharedStorage
     * @param ExampleFactoryInterface $slideshowBlockExampleFactory
     * @param ObjectManager           $slideshowBlockManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ExampleFactoryInterface $slideshowBlockExampleFactory,
        ExampleFactoryInterface $imagineBlockExampleFactory,
        ObjectManager $slideshowBlockManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->slideshowBlockExampleFactory = $slideshowBlockExampleFactory;
        $this->imagineBlockExampleFactory = $imagineBlockExampleFactory;
        $this->slideshowBlockManager = $slideshowBlockManager;
    }

    /**
     * @Given the store has default slideshows
     */
    public function theStoreHasDefaultSlideshows()
    {
        $slideshowBlock = $this->slideshowBlockExampleFactory->create(['title' => 'test', 'name' => 'test']);

        $imagine = $this->imagineBlockExampleFactory->create(['name' => 'test', 'label' => 'test', 'image' => 'book.jpg']);

        $slideshowBlock->addChildren($imagine);

        $this->slideshowBlockManager->persist($slideshowBlock);
        $this->slideshowBlockManager->flush();

        $this->sharedStorage->set('slideshow_block', $slideshowBlock);
    }

    /**
     * @Given the store has slideshow :title with name :name
     */
    public function theStoreHasSlideshowWithName($title, $name)
    {
        $slideshowBlock = $this->slideshowBlockExampleFactory->create(['title' => $title, 'name' => $name]);

        $this->slideshowBlockManager->persist($slideshowBlock);
        $this->slideshowBlockManager->flush();

        $this->sharedStorage->set('slideshow_block', $slideshowBlock);
    }

    /**
     * @Given /^(it) is not published yet$/
     */
    public function itIsNotPublishedYet(SlideshowBlock $slideshowBlock)
    {
        $slideshowBlock->setPublishable(false);

        $this->slideshowBlockManager->flush();
    }
}
