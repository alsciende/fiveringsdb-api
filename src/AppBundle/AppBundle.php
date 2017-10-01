<?php

namespace AppBundle;

use AppBundle\DependencyInjection\Compiler\DeckCheckPass;
use AppBundle\DependencyInjection\Compiler\DeckSearchPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function build (ContainerBuilder $container)
    {
        $container->addCompilerPass(new DeckCheckPass());
        $container->addCompilerPass(new DeckSearchPass());
    }
}
