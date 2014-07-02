<?php
namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceEvent;
use Sylius\Component\Core\Model\Product;

class PreUpdateResourceListener
{
    public function preProductUpdate(ResourceEvent $event)
    {
    	$resource = $event->getSubject();
        if( $resource instanceof Product )
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
