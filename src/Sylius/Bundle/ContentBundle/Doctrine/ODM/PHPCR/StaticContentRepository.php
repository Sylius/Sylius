<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository;

/**
 * Static content repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StaticContentRepository extends DocumentRepository
{
    public function findStaticContent($id)
    {
        return $this->find('/cms/pages/'.$id);
    }
}
