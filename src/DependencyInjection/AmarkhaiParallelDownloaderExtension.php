<?php

namespace Amarkhai\ParallelDownloaderBundle\DependencyInjection;

use Amarkhai\ParallelDownloaderBundle\DownloadManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AmarkhaiParallelDownloaderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(DownloadManager::class);

        $definition->setArgument(1, $config['download_files_folder']);
        $definition->setArgument(2, $config['download_retry']);
        $definition->setArgument(3, $config['download_concurrency']);
    }
}