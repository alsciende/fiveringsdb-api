<?php

namespace AppBundle\DependencyInjection\Compiler;

use AppBundle\Service\DeckSearchService;
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
        if ($container->has(DeckSearchService::class) === false) {
            return;
        }

        $definition = $container->findDefinition(DeckSearchService::class);

        $taggedServices = $container->findTaggedServiceIds('app.deck_search');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addService', [new Reference($id)]);
        }
    }
}