<?php

namespace Tests\Feature;

use App\GraphModels\Course;
use App\GraphModels\Covers;
use App\GraphModels\GraphModel;
use App\GraphModels\Relationship;
use App\GraphModels\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class TopicControllerTest extends TestCase
{
    public function test_topic_form_has_correct_props(): void
    {
        $this->createCourses(7);
        $this->createTopics(5);

        $this->get(route('topics.form'))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Topic/Form')
                    ->has('initialTopics', 5)
                    ->has('allCourses', 7)
                    ->has('coverageLevels'),
            );
    }

    public function test_visualization_page_has_correct_props(): void
    {
        $courseIds = $this->createCourses(3);
        $topicIds = $this->createTopics(5);

        for ($i = 0; $i < 2; $i++) {
            Covers::create($courseIds[$i], $topicIds[$i], [
                'coverage_level' => 'beginner',
            ]);
        }

        $this->get(route('topics.visualization'))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Topic/Visualization')
                    ->has('courses', 2)
                    ->has('topics', 5)
                    ->has('coursesWithTopics', 2)
                    ->has('coverageLevels'),
            );
    }

    public function test_get_courses(): void
    {
        $topicId = $this->createTopics(1)[0];
        $courseIds = $this->createCourses(5);

        foreach ($courseIds as $courseId) {
            Covers::create($courseId, $topicId, [
                'coverage_level' => 'beginner',
            ]);
        }

        $responseCourses = $this->get(
            route('topics.get_courses', $topicId),
        )->assertOk()->original;

        $this->assertCount(5, $responseCourses);
    }

    public function test_store(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->post(route('topics.store'), [
            'name' => 'test topic name',
        ]);

        $allTopics = Topic::all();

        $this->assertCount(1, $allTopics);
        $this->assertEquals('test topic name', $allTopics[0]['name']);
        $response->assertSessionHas('data', function (array $value) use (
            $allTopics,
        ) {
            return $value['id'] === $allTopics[0]['id'];
        });
    }

    public function test_update(): void
    {
        //        $this->withoutExceptionHandling();

        $topicId = Topic::create(['name' => 'new topic name']);

        $this->patch(route('topics.update', $topicId), [
            'name' => 'changed name',
        ]);

        $this->assertEquals('changed name', Topic::find($topicId)['name']);
    }

    public function test_destroy(): void
    {
        $this->withoutExceptionHandling();

        $topicIdToDelete = Topic::create(['name' => 'new topic name 1']);
        Topic::create(['name' => 'new topic name 2']);

        $this->assertCount(2, Topic::all());

        $this->delete(route('topics.destroy', $topicIdToDelete));

        $this->assertCount(1, Topic::all());
    }

    protected function setUp(): void
    {
        parent::setUp();

        GraphModel::runQueryInTransaction('MATCH (n) DETACH DELETE n');
    }
}
