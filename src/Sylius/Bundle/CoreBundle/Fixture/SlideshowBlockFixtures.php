<?php

namespace Sylius\Bundle\CoreBundle\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\Factory\ImagineBlockExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\SlideShowBlockExampleFactory;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Vidy Videni<videni@foxmail.com>
 */
final class SlideshowBlockFixtures extends AbstractFixture
{
    /**
     * @var SlideShowBlockExampleFactory
     */
    private $slideShowBlockExampleFactory;

    /**
     * @var ImagineBlockExampleFactory
     */
    private $imagineBlockExampleFactory;

    /**
     * @var RepositoryInterface
     */
    protected $slideshowBlockRepository;

    public function __construct(SlideShowBlockExampleFactory $slideShowBlockExampleFactory,
                                ImagineBlockExampleFactory $imagineBlockExampleFactory,
                                RepositoryInterface $slideshowBlockRepository
    ) {
        $this->slideShowBlockExampleFactory = $slideShowBlockExampleFactory;
        $this->imagineBlockExampleFactory = $imagineBlockExampleFactory;
        $this->slideshowBlockRepository = $slideshowBlockRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'slideshow_block';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        $slideshowBlock = $this->slideShowBlockExampleFactory->create([
            'name' => 'slideshow-home',
            'enabled' => true,
            'publishable' => true,

        ]);

        foreach (['books.jpg', 'stickers.jpg', 'mugs.jpg'] as $image) {
            $imagePath = sprintf('%s/../Resources/fixtures/%s', __DIR__, $image);

            $info = pathinfo($imagePath);
            $fileName = basename($imagePath, '.'.$info['extension']);

            $imagineBlock = $this->imagineBlockExampleFactory->create([
                'label' => $fileName,
                'name' => $fileName,
                'publishable' => true,
                'image' => $imagePath,
                'parentDocument' => $slideshowBlock,
            ]);

            $slideshowBlock->addChild($imagineBlock);
        }

        $this->slideshowBlockRepository->add($slideshowBlock);
    }
}
