<?php

namespace Amarkhai\ParallelDownloaderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('amarkhai_parallel_downloader');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('download_files_folder')->defaultValue('%amarkhai_parallel_downloader.download_files_folder%')->end()
                ->integerNode('download_retry')->defaultValue('%amarkhai_parallel_downloader.download_retry%')->end()
                ->integerNode('download_concurrency')->defaultValue('%amarkhai_parallel_downloader.download_concurrency%')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}