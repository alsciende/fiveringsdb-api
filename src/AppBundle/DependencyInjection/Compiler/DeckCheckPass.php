<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of DeckCheckerPass
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckCheckPass implements CompilerPassInterface
{
    public function process (ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if ($container->has('app.deck_validator') === false) {
            return;
        }

        $definition = $container->findDefinition('app.deck_validator');

        // find all service IDs with the app.mail_transport tag
        $taggedServices = $container->findTaggedServiceIds('app.deck_check');

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the ChainTransport service
            $definition->addMethodCall('addDeckCheck', [new Reference($id)]);
        }
    }
}
