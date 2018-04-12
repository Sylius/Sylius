<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\FilesAwareInterface;
use Sylius\Component\Core\Uploader\FileUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class FilesUploadListener
{
    /**
     * @var FileUploaderInterface
     */
    private $uploader;

    /**
     * @param FileUploaderInterface $uploader
     */
    public function __construct(FileUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param GenericEvent $event
     */
    public function uploadFiles(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, FilesAwareInterface::class);

        $this->uploadSubjectFiles($subject);
    }

    /**
     * @param FilesAwareInterface $subject
     */
    private function uploadSubjectFiles(FilesAwareInterface $subject): void
    {
        $files = $subject->getFiles();
        foreach ($files as $file) {
            if ($file->hasFile()) {
                $this->uploader->upload($file);
            }

            // Upload failed? Let's remove that file.
            if (null === $file->getPath()) {
                $files->removeElement($file);
            }
        }
    }
}
