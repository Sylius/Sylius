<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository;

/**
 * Page repository.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PageRepository extends DocumentRepository
{
    public function findPage($id)
    {
        return $this->find('/cms/pages/'.$id);
    }
}
