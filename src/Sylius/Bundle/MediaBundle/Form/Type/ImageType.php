<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MediaBundle\Form\Type;

use Doctrine\ODM\PHPCR\DocumentManager;
use Sylius\Bundle\MediaBundle\Form\DataTransformer\PathToDocumentTransformer;
use Sylius\Bundle\MediaBundle\Form\EventSubscriber\SyncImageMediaIdSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class ImageType extends AbstractResourceType
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param DocumentManager $documentManager
     */
    public function __construct(
        $dataClass,
        array $validationGroups,
        DocumentManager $documentManager
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->documentManager = $documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder
                    ->create('media', 'cmf_media_image')
                    ->addViewTransformer(new PathToDocumentTransformer($this->documentManager))
            )
            ->addEventSubscriber(new SyncImageMediaIdSubscriber($this->documentManager))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_image';
    }
}
