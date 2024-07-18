<?php

namespace Tests;

use App\GraphModels\Course;
use App\GraphModels\Topic;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TopicControllerTest;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;

    public function createCourses(int $numberToCreate): array
    {
        $courseNames = $this->faker->words($numberToCreate);

        $ids = [];

        foreach ($courseNames as $index => $courseName) {
            $ids[] = Course::create([
                'number' => $index,
                'name' => $courseName,
            ]);
        }

        return $ids;
    }

    public function createTopics(int $numberToCreate): array
    {
        $topicNames = $this->faker->words($numberToCreate);

        $ids = [];

        foreach ($topicNames as $topicName) {
            $ids[] = Topic::create(['name' => $topicName]);
        }

        return $ids;
    }
}
