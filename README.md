# silex-elasticsearch-profiler
## Features

This Library provide an Elasticsearch profiler for the symfony web toolbar on Silex apps.
This is largely inspired from "m6web/elasticsearch-bundle"

## Usage

### Installation
You must first add the library to your `composer.json`:

```json
    "require-dev": {
        "enrj/silex-elasticsearch-profiler": "dev-master"
    }
```

Then register the service provider:

```php
<?php
    public function registerBundles()
    {
        $app->register(new ElasticSearchProfilerServiceProvider());
    }
```

Finally you have to set the event handler for your ElasticSearch Client:

 ```php
 <?php
         $builder = ClientBuilder::create();
         if (class_exists('Enrj\SilexElasticsearchProfiler\Handler\EventHandler')) {
             $builder->setHandler(new EventHandler($app['dispatcher'], ClientBuilder::defaultHandler()));
         }

         $client = $builder->build();
```