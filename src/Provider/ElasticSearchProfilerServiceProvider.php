<?php

namespace Enrj\SilexElasticsearchProfiler\Provider;

use Elasticsearch\ClientBuilder;
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
    /**
     * @var ElasticsearchDataCollector
     */
    protected $collector;

    public function register(Container $app)
    {
        $this->collector = new ElasticsearchDataCollector();
        $app['data_collector.templates'] = $app->extend('data_collector.templates', function ($templates) {
            $templates[] = array('elasticsearch', '@ElasticsearchProfiler/profiler.html.twig');

            return $templates;
        });
        $app['data_collector.elasticsearch'] = function () {
            return $this->collector;
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
            return __DIR__.'/../../res';
        };
    }

    public function boot(Application $app)
    {
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener('elasticsearch', array($this->collector, 'handleEvent'));
    }
}
