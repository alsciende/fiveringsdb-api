<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of DeckSearchPass
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckSearchPass implements CompilerPassInterface
{
    public function process (ContainerBuilder $container)
    {
        if ($container->has('app.deck_search') === false) {
            return;
        }

        $definition = $container->findDefinition('app.deck_search');

        $taggedServices = $container->findTaggedServiceIds('app.deck_search');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addService', [new Reference($id)]);
        }
    }
}