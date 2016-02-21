<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frontend homepage controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class HomepageController extends Controller
{
    public function mainAction(Request $request)
    {
        $manager = $this->get('sylius.settings.manager');

        $zone = $this->get('sylius.repository.zone')->findAll()[0];

        $settings = $manager->load('sylius_taxation');
//        $settings['default_tax_zone'] = $zone;

//        echo '<pre>';
        var_dump($settings->getParameters()['default_tax_zone']->getId());
//
        $manager->save($settings);

        var_dump($settings->getParameters()['default_tax_zone']->getId());


        die;

        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig');
    }
}
