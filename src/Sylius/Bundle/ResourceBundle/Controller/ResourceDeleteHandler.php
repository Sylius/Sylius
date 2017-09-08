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

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Exception\DeleteHandlingException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ResourceDeleteHandler implements ResourceDeleteHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ResourceInterface $resource, RepositoryInterface $repository): void
    {
        try {
            $repository->remove($resource);
        } catch (\Exception $exception) {
            throw new DeleteHandlingException();
        }
    }
}
