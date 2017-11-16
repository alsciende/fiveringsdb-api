<?php

namespace AppBundle\DependencyInjection\Compiler;

use AppBundle\Service\DeckValidator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        if ($container->has(DeckValidator::class) === false) {
            return;
        }

        $definition = $container->findDefinition(DeckValidator::class);

        $taggedServices = $container->findTaggedServiceIds('app.deck_check');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addDeckCheck', [new Reference($id)]);
        }
    }
}
