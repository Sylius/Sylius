<?php
namespace Ant\Bundle\CommentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AntCommentBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSCommentBundle';
    }
}
