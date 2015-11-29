<?php

namespace Sylius\Bundle\RbacBundle\Security\Role;

interface InflectorInterface
{
    /**
     * Convert "content.editor" to "ROLE_CONTENT_EDITOR",
     * "ROLE_" being the configured prefix.
     *
     * @param string $name
     *
     * @return string
     */
    public function toSecurityRole($name);

    /**
     * Convert "ROLE_CONTENT_EDITOR" to "content.editor"
     * "ROLE_" being the configured prefix.
     *
     * @param string $name
     *
     * @return string
     */
    public function toRbacRole($name);
}
