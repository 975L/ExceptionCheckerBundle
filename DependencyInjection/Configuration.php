<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('c975_l_exception_checker');

        $rootNode
            ->children()
                ->scalarNode('roleNeeded')
                    ->defaultValue('ROLE_ADMIN')
                ->end()
                ->scalarNode('redirectExcluded')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}