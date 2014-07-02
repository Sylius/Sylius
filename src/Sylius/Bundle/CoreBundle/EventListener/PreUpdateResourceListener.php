<?php

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceEvent;

class PreUpdateResourceListener
{
    public function preProductUpdate(ResourceEvent $event)
    {

    	$resource = $event->getSubject();
        if( $resource instanceof \Sylius\Component\Core\Model\Product )
        {
            $images = $resource->getImages();
            foreach ( $images as $image ) {

                if( $image->getPath() == null )
                {
                    $resource->getImages()->removeElement( $image);
                }
            }
        }
    }
}
