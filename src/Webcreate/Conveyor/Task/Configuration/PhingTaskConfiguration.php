<?php

/*
 * This file is part of the Conveyor package.
 *
 * (c) Jeroen Fiege <jeroen@webcreate.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webcreate\Conveyor\Task\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class PhingTaskConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('task');

        $rootNode
            ->children()
                ->scalarNode('buildfile')->defaultNull()->end()
                ->arrayNode('target')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($v) {
                            return array($v);
                        })
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
