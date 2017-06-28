<?php

namespace Enrj\SilexElasticsearchProfiler\Provider;

use Enrj\SilexElasticsearchProfiler\DataCollector\ElasticsearchDataCollector;
use Enrj\SilexElasticsearchProfiler\Handler\EventHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ElasticSearchProfilerServiceProvider implements ServiceProviderInterface, BootableProviderInterface, EventListenerProviderInterface
{
    public function register(Container $app)
    {
dump("1");
        $app['elasticsearch.options']['handler'] = new EventHandler(
            $app['dispatcher'],
            isset($app['elasticsearch.options']['handler']) ? $app['elasticsearch.options']['handler'] : ClientBuilder::defaultHandler()
        );
        $app['data_collector.templates'] = $app->extend('data_collector.templates', function ($templates) {
            $templates[] = array('elasticsearch', '@ElasticsearchProfiler/profiler.html.twig');

            return $templates;
        });
        $app['data_collector.elasticsearch'] = function () {
            return new ElasticsearchDataCollector();
        };
        $app->extend('data_collectors', function ($collectors, $app) {
            $collectors['elasticsearch'] = function ($app) {
                return $app['data_collector.elasticsearch'];
            };

            return $collectors;
        });
        $app->extend('twig.loader.filesystem', function ($loader, $app) {
            $loader->addPath($app['elasticsearchprofiler.templates_path'], 'ElasticsearchProfiler');

            return $loader;
        });
        $app['elasticsearchprofiler.templates_path'] = function () {
            return __DIR__.'/res';
        };
    }

    public function boot(Application $app)
    {
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        //$dispatcher->addSubscriber(new DumpListener($app['var_dumper.cloner'], $app['data_collector.dump']));
    }
}
