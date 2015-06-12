<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Default product images.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadImagesData extends DataFixture
{
    protected $path = '/../../Resources/fixtures';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $finder = new Finder();
        $uploader = $this->get('sylius.image_uploader');

        foreach ($finder->files()->in(__DIR__.$this->path) as $img) {
            $image = $this->getProductVariantImageRepository()->createNew();
            $image->setFile(new UploadedFile($img->getRealPath(), $img->getFilename()));
            $uploader->upload($image);

            $manager->persist($image);

            $this->setReference('Sylius.Image.'.$img->getBasename('.jpg'), $image);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 5;
    }
}
