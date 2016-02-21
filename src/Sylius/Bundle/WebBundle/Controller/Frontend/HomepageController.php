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

        $settings = $manager->load('sylius_general');
        $settings['title'] = 'Heeey!!!';

        echo '<pre>';
        print_r($settings->getParameters());

        $manager->save($settings);
        die;

        echo 123;
        die;

        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig');
    }
}
