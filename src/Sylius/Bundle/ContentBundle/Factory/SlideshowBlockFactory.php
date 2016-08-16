<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ContentBundle\Document\SlideshowBlock;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Vidy Videni <vidy.videni@gmail.com>
 */
final class SlideshowBlockFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @var ObjectManager
     */
    private $documentManager;

    /**
     * @var string
     */
    private $blockParentPath;

    /**
     * @param FactoryInterface $decoratedFactory
     * @param ObjectManager $documentManager
     * @param string $staticContentParentPath
     */
    public function __construct(FactoryInterface $decoratedFactory, ObjectManager $documentManager, $blockParentPath)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->documentManager = $documentManager;
        $this->blockParentPath = $blockParentPath;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        /** @var SlideshowBlock $slideshowBlock */
        $slideshowBlock = $this->decoratedFactory->createNew();

        $slideshowBlock->setParentDocument($this->documentManager->find(null, $this->blockParentPath));

        return $slideshowBlock;
    }
}
