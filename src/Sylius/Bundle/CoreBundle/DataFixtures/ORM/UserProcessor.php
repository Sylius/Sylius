<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jago
 * Date: 10/6/13
 * Time: 3:29 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;


use FOS\UserBundle\Model\UserManager;
use Nelmio\Alice\ProcessorInterface;

class UserProcessor implements ProcessorInterface
{
    private $manager;

    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;
    }

    public function preProcess($user)
    {
        $this->manager->updateCanonicalFields($user);
        $this->manager->updatePassword($user);
    }

    public function postProcess($user)
    {
        return;
    }
}