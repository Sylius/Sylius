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

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

@trigger_error(sprintf('The "%s" class is deprecated since Sylius 1.3. Doctrine MongoDB and PHPCR support will no longer be supported in Sylius 2.0.', NameFilterListener::class), \E_USER_DEPRECATED);

/**
 * Filter the node name field, replacing invalid characters with a substitute
 * characters.
 *
 * @see http://www.day.com/specs/jcr/2.0/3_Repository_Model.html#3.2.2%20Local%20Names
 * @see https://github.com/phpcr/phpcr-utils/blob/master/src/PHPCR/Util/PathHelper.php#L95
 */
class NameFilterListener
{
    /** @var DocumentManagerInterface */
    private $documentManager;

    /** @var string */
    private $replacementCharacter;

    public function __construct(
        DocumentManagerInterface $documentManager,
        $replacementCharacter = ' '
    ) {
        $this->documentManager = $documentManager;
        $this->replacementCharacter = $replacementCharacter;
    }

    public function onEvent(ResourceControllerEvent $event)
    {
        $document = $event->getSubject();

        $metadata = $this->documentManager->getClassMetadata(get_class($document));

        if (null === $nameField = $metadata->nodename) {
            throw new \RuntimeException(sprintf(
                'In order to use the node name filter on "%s" it is necessary to map a field as the "nodename"',
                get_class($document)
            ));
        }

        $name = $metadata->getFieldValue($document, $nameField);
        $name = preg_replace('/\\/|:|\\[|\\]|\\||\\*/', $this->replacementCharacter, $name);
        $metadata->setFieldValue($document, $nameField, $name);
    }
}
