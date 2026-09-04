<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class ImageType extends AbstractResourceType
{
    private const CACHED_UPLOADS_SUBDIR = 'sylius_cached_uploads';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'label' => 'sylius.form.image.type',
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'label' => 'sylius.form.image.file',
            ])
            ->add('cachedFilePath', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('cachedFileName', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();

            if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
                $uploadedFile = $data['file'];
                $tempDir = $this->getCachedUploadsDir();

                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }

                $cachedFileName = uniqid('cached_', true) . '_' . $uploadedFile->getClientOriginalName();
                copy($uploadedFile->getPathname(), $tempDir . '/' . $cachedFileName);

                $data['cachedFilePath'] = $cachedFileName;
                $data['cachedFileName'] = $uploadedFile->getClientOriginalName();

                $event->setData($data);

                return;
            }

            if (empty($data['file']) && !empty($data['cachedFilePath']) && !empty($data['cachedFileName'])) {
                $tempDir = $this->getCachedUploadsDir();
                $cachedFilePath = $tempDir . '/' . basename($data['cachedFilePath']);

                if (file_exists($cachedFilePath)) {
                    $data['file'] = new UploadedFile(
                        $cachedFilePath,
                        $data['cachedFileName'],
                        null,
                        null,
                        true,
                    );
                    $event->setData($data);
                }
            }
        });
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_image';
    }

    public static function getCachedUploadsDirPath(): string
    {
        return sys_get_temp_dir() . '/' . self::CACHED_UPLOADS_SUBDIR;
    }

    private function getCachedUploadsDir(): string
    {
        return self::getCachedUploadsDirPath();
    }
}
