<?php
namespace KbizeCli;

class TaskCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $tasks = $this->sampleTasks();
        $taskCollection = new TaskCollection($tasks);

        $this->assertEquals($tasks, $taskCollection->tasks());
    }

    public function testFilterCollectionWithLowercaseFilterAndLowercaseValue()
    {
        $tasks = $this->sampleTasks();
        $taskCollection = new TaskCollection($tasks);

        $this->assertEquals(
            [['Lorem', 'ipsum', 'dolor', 'sit', 'amet']],
            $taskCollection->filter(['ipsum'])
        );
    }

    public function testFilterCollectionWithLowercaseFilterAndUppercaseValue()
    {
        $tasks = $this->sampleTasks();
        $taskCollection = new TaskCollection($tasks);

        $this->assertEquals(
            [['Lorem', 'ipsum', 'dolor', 'sit', 'amet']],
            $taskCollection->filter(['lorem'])
        );
    }

    public function testFilterCollectionWithMultipleExistingFilters()
    {
        $tasks = $this->sampleTasks();
        $taskCollection = new TaskCollection($tasks);

        $this->assertEquals(
            [['Cum', 'sociis', 'natoque', 'penatibus', 'et', 'magnis']],
            $taskCollection->filter(['sociis', 'pena'])
        );
    }

    public function testFilterCollectionWithMultipleNotAllExistingFilters()
    {
        $tasks = $this->sampleTasks();
        $taskCollection = new TaskCollection($tasks);

        $this->assertEquals(
            [],
            $taskCollection->filter(['sociis', 'not-exists'])
        );
    }

    public function testFilterCollectionWithMultipleNotAllExistingFiltersWithOrStrategy()
    {
        $tasks = $this->sampleTasks();
        $taskCollection = new TaskCollection($tasks);

        $this->assertEquals(
            [['Cum', 'sociis', 'natoque', 'penatibus', 'et', 'magnis']],
            $taskCollection->filter(['sociis', 'not-exists'], false)
        );
    }

    public function testFilterCollectionWithFilterOnSpecificKey()
    {
        $t1 = [
            'title' => 'foo',
            'description' => 'bar',
        ];

        $t2 = [
            'title' => 'bar',
            'description' => 'foo',
        ];

        $tasks = [$t1, $t2];

        $taskCollection = new TaskCollection($tasks);

        $this->assertEquals(
            [$t1],
            $taskCollection->filter(['title=foo'])
        );
    }

    public function testFilterCollectionOnSubArray()
    {
        $t1 = [
            'child' => [
                'title' => 'foo',
                'description' => 'bar',
            ]
        ];

        $t2 = [
            'child' => [
                'title' => 'baz',
                'description' => 'qux',
            ]
        ];

        $tasks = [$t1, $t2];

        $taskCollection = new TaskCollection($tasks);

        $this->assertEquals(
            [$t1],
            $taskCollection->filter(['foo'])
        );
    }

    public function testFilterCollectionOnSubArrayWithSpecificKey()
    {
        $this->markTestIncomplete();

        $t1 = [
            'child' => [
                'title' => 'foo',
                'description' => 'bar',
            ]
        ];

        $t2 = [
            'child' => [
                'title' => 'baz',
                'description' => 'qux',
            ]
        ];

        $tasks = [$t1, $t2];

        $taskCollection = new TaskCollection($tasks);

        $this->assertEquals(
            [$t1],
            $taskCollection->filter(['child.title=foo'])
        );
    }

    private function sampleTasks()
    {
        return [
            ['Lorem', 'ipsum', 'dolor', 'sit', 'amet'],
            ['Cum', 'sociis', 'natoque', 'penatibus', 'et', 'magnis'],
            ['Sed', 'ultricies', 'fringilla', 'tristique'],
            ['Morbi', 'interdum', 'felis', 'a augue', 'bibendum', 'suscipit'],
            [new \stdClass(), ['foo', 'bar']],
        ];
    }
}
