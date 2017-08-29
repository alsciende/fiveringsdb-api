<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use AppBundle\DependencyInjection\Compiler\DeckCheckPass;

class AppBundle extends Bundle
{
    public function build (ContainerBuilder $container)
    {
        $container->addCompilerPass(new DeckCheckPass());
    }
}
