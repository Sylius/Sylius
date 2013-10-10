<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Nelmio\Alice\ProcessorInterface;
use Sylius\Bundle\CoreBundle\Model\VariantInterface;
use Sylius\Bundle\CoreBundle\Uploader\ImageUploaderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Intl\Intl;

/**
 * Image processor : uploads the variant image
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ImageProcessor implements ProcessorInterface
{
    protected $uploader;
    protected $kernelRootDirectory;
    protected $files = array();

    public function __construct(ImageUploaderInterface $uploader, $kernelRootDirectory)
    {
        $this->uploader = $uploader;
        $this->kernelRootDirectory = $kernelRootDirectory;

        $finder = new Finder();
        foreach ($finder->files()->in($this->kernelRootDirectory . '/../web/fixtures') as $file) {
            $this->files[$file->getRelativePathname()] = $file;
        }
    }

    public function preProcess($image)
    {
        $file = $this->findFile($image->getVariant());

        $image->setFile(new UploadedFile($file->getRealPath(), $file->getFilename()));
        $this->uploader->upload($image);
    }

    public function postProcess($order)
    {
        return;
    }

    /**
     * Return the image file associated to the given variant.
     *
     * @param VariantInterface $variant
     * @return mixed
     */
    protected function findFile(VariantInterface $variant)
    {
        $pieces = explode(' ', $variant->getProduct()->getName());

        return $this->files[strtolower($pieces[0]) . '.jpg'];
    }
}