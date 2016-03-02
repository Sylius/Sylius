<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MediaBundle\Form\DataTransformer;

use Doctrine\ODM\PHPCR\DocumentManager;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforms a path string to an actual document.
 * This transformer helps to get the real document when user has selected a document from the elFinder browser.
 *
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class PathToDocumentTransformer implements DataTransformerInterface
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(
        DocumentManager $documentManager
    ) {
        $this->documentManager = $documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (is_string($value)) {
            return $this->documentManager->find(null, $value);
        }

        return $value;
    }
}
