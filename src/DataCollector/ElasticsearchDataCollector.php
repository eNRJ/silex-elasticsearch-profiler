<?php

namespace Enrj\SilexElasticsearchProfiler\DataCollector;

use Enrj\SilexElasticsearchProfiler\EventDispatcher\ElasticsearchEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * ElasticsearchDataCollector.
 */
class ElasticsearchDataCollector extends DataCollector
{
    /**
     * ElasticsearchDataCollector constructor.
     */
    public function __construct()
    {
        $this->data = [
            'queries' => [],
            'total_execution_time' => 0,
        ];
    }

    /**
     * @param ElasticsearchEvent $event
     */
    public function handleEvent(ElasticsearchEvent $event)
    {
        $body = explode("\n", $event->getBody());
        foreach ($body as $key => &$el) {
            if (null === $el = json_decode($el)) {
                unset($body[$el]);
            }
        }
        $query = array(
            'method' => $event->getMethod(),
            'uri' => $event->getUri(),
            'headers' => $this->varToString($event->getHeaders()),
            'status_code' => $event->getStatusCode(),
            'duration' => $event->getDuration(),
            'took' => $event->getTook(),
            'body' => $body,
            'error' => $event->getError(),
        );
        $this->data['queries'][] = $query;
        $this->data['total_execution_time'] += $query['duration'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'elasticsearch';
    }

    /**
     * Get queries.
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->data['queries'];
    }

    /**
     * Get total execution time.
     *
     * @return float
     */
    public function getTotalExecutionTime()
    {
        return $this->data['total_execution_time'];
    }
}
