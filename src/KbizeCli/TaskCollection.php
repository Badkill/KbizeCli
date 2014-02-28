<?php
namespace KbizeCli;

class TaskCollection
{
    private $tasks;

    public function __construct(array $tasks = [])
    {
        $this->tasks = $tasks;
    }

    public function tasks()
    {
        return $this->tasks;
    }

    public function filter(array $filters = [], $useAnd = true)
    {
        $collectedTasks = [];

        if ($useAnd) {
            $strategy = new AndMatcherStrategy();
        } else {
            $strategy = new OrMatcherStrategy();
        }

        foreach ($this->tasks as $task) {
            if ($strategy->match($task, $filters)) {
                $collectedTasks[] = $task;
            }
        }

        return $collectedTasks;
    }
}

class AndMatcherStrategy extends MatcherStrategy
{
    public function match($collection, $filters)
    {
        $theCollectionMatch = true;

        foreach ($filters as $filter) {
            $theCollectionMatch &= $this->collectionMatch($collection, $filter);
        }

        return $theCollectionMatch;
    }
}

class OrMatcherStrategy extends MatcherStrategy
{
    public function match($collection, $filters)
    {
        $theCollectionMatch = false;

        foreach ($filters as $filter) {
            $theCollectionMatch |= $this->collectionMatch($collection, $filter);
        }

        return $theCollectionMatch;
    }
}

abstract class MatcherStrategy
{
    abstract function match($collection, $filters);

    protected function collectionMatch($collection, $filter)
    {
        $keyValueFilter = $this->keyValueFilter($filter);

        foreach ($collection as $field => $fieldValue) {
            if ($keyValueFilter['key'] && $field != $keyValueFilter['key']) {
                continue;
            }

            if (is_array($fieldValue)) {
                if ($this->collectionMatch($fieldValue, $filter)) {
                    return true;
                }

                continue;
            }

            if (is_object($fieldValue)) {
                if (is_callable([$fieldValue, '__toString'])) {
                    $fieldValue = $fieldValue->__toString();
                } else {
                    continue;
                }
            }

            if (strpos(strtolower($fieldValue), strtolower($keyValueFilter['value'])) !== false) {
                return true;
            }
        }
    }

    private function keyValueFilter($filter)
    {
        if (($pos = strpos($filter, '=')) !== false) {
            return [
                'key' => substr($filter, 0, $pos),
                'value' => substr($filter, $pos + 1)
            ];
        }

        return [
            'key' => null,
            'value' => $filter
        ];
    }
}
