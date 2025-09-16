<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;
use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (DoctrineConfig $doctrine, FrameworkConfig $framework, ContainerConfigurator $container): void {
    $dbal = $doctrine->dbal();
    $dbal->connection('default')
        ->url(env('resolve:DATABASE_URL'))
        ->profilingCollectBacktrace(param('kernel.debug'))
        ->useSavepoints(true)
    ;

    $orm = $doctrine->orm();
    $orm
        ->autoGenerateProxyClasses(true)
        ->enableLazyGhostObjects(true)
    ;

    $defaultEntityManager = $orm->entityManager('default');

    $defaultEntityManager
        ->connection('default')
        ->namingStrategy('doctrine.orm.naming_strategy.underscore_number_aware')
        ->autoMapping(false)
    ;

    $defaultEntityManager
        ->mapping('App\Entity')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/Entity')
        ->prefix('App\Entity')
    ;

    if ('test' === $container->env()) {
        $dbal->connection('default')->dbnameSuffix('_test_%env(TEST_TOKEN)%');
    }

    if ('prod' === $container->env()) {
        $orm->autoGenerateProxyClasses(false)
            ->proxyDir('%kernel.build_dir%/doctrine/orm/Proxies')
        ;

        $defaultEntityManager->queryCacheDriver()->type('pool')->pool('doctrine.system_cache_pool');
        $defaultEntityManager->metadataCacheDriver()->type('pool')->pool('doctrine.system_cache_pool');
        $defaultEntityManager->resultCacheDriver()->type('pool')->pool('doctrine.result_cache_pool');

        $cache = $framework->cache();
        $cache->pool('doctrine.system_cache_pool')->adapters(['cache.system']);
        $cache->pool('doctrine.result_cache_pool')->adapters(['cache.app']);
    }
};
