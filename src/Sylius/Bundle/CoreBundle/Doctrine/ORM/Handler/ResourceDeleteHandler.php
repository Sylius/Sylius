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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler;

use Doctrine\ORM\ORMException;
use Sylius\Bundle\ResourceBundle\Controller\ResourceDeleteHandlerInterface;
use Sylius\Component\Resource\Exception\DeleteHandlingException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ResourceDeleteHandler implements ResourceDeleteHandlerInterface
{
    /**
     * @var ResourceDeleteHandlerInterface
     */
    private $decoratedHandler;

    /**
     * @param ResourceDeleteHandlerInterface $decoratedHandler
     */
    public function __construct(ResourceDeleteHandlerInterface $decoratedHandler)
    {
        $this->decoratedHandler = $decoratedHandler;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DeleteHandlingException
     */
    public function handle(ResourceInterface $resource, RepositoryInterface $repository): void
    {
        try {
            $this->decoratedHandler->handle($resource, $repository);
        } catch (ORMException $exception) {
            throw new DeleteHandlingException();
        }
    }
}
