<?php

namespace Rodger\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RodgerUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
