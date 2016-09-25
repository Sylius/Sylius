<?php

namespace Sylius\Bundle\ResourceBundle;

/**
 * Events that are issued by the resource controller.
 */
final class ResourceControllerEvents
{
    /**
     * Issued when a resource is shown.
     */
    const SHOW = 'sylius.resource.controller.show';

    /**
     * Issued when the index page of a resource is displayed.
     */
    const INDEX  = 'sylius.resource.controller.index';

    /**
     * Issued before a resource is updated.
     */
    const PRE_UPDATE = 'sylius.resource.controller.pre_update';

    /**
     * Issued after a resource has been updated.
     */
    const POST_UPDATE = 'sylius.resource.controller.post_update';

    /**
     * Issued before a resource is deleted.
     */
    const PRE_DELETE = 'sylius.resource.controller.pre_delete';

    /**
     * Issued after a resource has been deleted.
     */
    const POST_DELETE = 'sylius.resource.controller.post_delete';

    /**
     * Issued after a resource is created.
     */
    const PRE_CREATE = 'sylius.resource.controller.pre_create';

    /**
     * Issued after a resource has been created.
     */
    const POST_CREATE = 'sylius.resource.controller.post_create';
}
