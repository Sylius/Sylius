<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadImagesData extends DataFixture
{
    protected $path = '/../../Resources/fixtures';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $finder = new Finder();
        $uploadImageHelper = $this->get('cmf_media.persistence.phpcr.upload_image_helper');

        foreach ($finder->files()->in(__DIR__.$this->path) as $imageFile) {
            $file = new UploadedFile($imageFile->getRealPath(), $imageFile->getFilename(), null, null, null, true);
            $media = $uploadImageHelper->handleUploadedFile($file, 'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image');
            $manager->persist($media);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
