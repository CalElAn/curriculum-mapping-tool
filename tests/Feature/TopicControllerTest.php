<?php

namespace Tests\Feature;

use App\GraphModels\Course;
use App\GraphModels\Covers;
use App\GraphModels\GraphModel;
use App\GraphModels\Relationship;
use App\GraphModels\Teaches;
use App\GraphModels\Topic;
use App\Http\Helpers\Helpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class TopicControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_topic_form_has_correct_props(): void
    {
        $this->createCourses(7);
        $this->createTopics(5);
        $this->createKnowledgeAreas(3);

        $this->actingAs($this->regularUser())
            ->get(route('topics.form'))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Topic/Form')
                    ->has('initialTopics.data', 5)
                    ->has('allCourses', 7)
                    ->has('allKnowledgeAreas', 3)
                    ->has('levels'),
            );
    }

    public function test_topic_form_can_be_filtered(): void
    {
        Topic::create(['name' => 'foo']);
        Topic::create(['name' => 'bar']);
        Topic::create(['name' => 'baz']);

        $this->actingAs($this->regularUser())
            ->get(route('topics.form', ['filter' => 'fo']))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Topic/Form')
                    ->has('initialTopics.data', 1)
                    ->has(
                        'initialTopics.data.0',
                        fn(Assert $page) => $page->where('name', 'foo')->etc(),
                    ),
            );

        $this->actingAs($this->regularUser())
            ->get(route('topics.form', ['filter' => 'ba']))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Topic/Form')
                    ->has('initialTopics.data', 2),
            );
    }
    public function test_topic_form_can_be_paginated(): void
    {
        $numberOfTopics = 13;
        $this->createTopics($numberOfTopics);

        $this->actingAs($this->regularUser())
            ->get(route('topics.form'))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Topic/Form')
                    ->has('initialTopics.data', Helpers::$perPage),
            );

        $this->actingAs($this->regularUser())
            ->get(route('topics.form', ['page' => 2]))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Topic/Form')
                    ->has(
                        'initialTopics.data',
                        $numberOfTopics - Helpers::$perPage,
                    ),
            );
    }

//    public function test_visualization_page_has_correct_props(): void
//    {
//        $courseIds = $this->createCourses(3);
//        $topicIds = $this->createTopics(5);
//
//        for ($i = 0; $i < 2; $i++) {
//            Covers::create($courseIds[$i], $topicIds[$i], [
//                'coverage_level' => 'beginner',
//            ]);
//        }

//        $this->actingAs($this->regularUser())
//            ->get(route('topics.visualization'))
//            ->assertOk()
//            ->assertInertia(
//                fn(Assert $page) => $page
//                    ->component('Topic/Visualization')
//                    ->has('courses', 2)
//                    ->has('topics', 5)
//                    ->has('coursesWithTopics', 2)
//                    ->has('coverageLevels'),
//            );
//    }

    public function test_get_courses(): void
    {
        $topicId = $this->createTopics(1)[0];
        $courseIds = $this->createCourses(5);

        foreach ($courseIds as $courseId) {
            Teaches::create($courseId, $topicId, [
                'level' => 'beginner',
            ]);
        }

        $responseCourses = $this->actingAs($this->regularUser())
            ->get(route('topics.get_courses', $topicId))
            ->assertOk()->original;

        $this->assertCount(5, $responseCourses);
    }

    public function test_get_knowledge_areas(): void
    {
        $topicId = $this->createTopics(1)[0];
        $knowledgeAreaIds = $this->createKnowledgeAreas(4);

        foreach ($knowledgeAreaIds as $knowledgeAreaId) {
            Covers::create($topicId, $knowledgeAreaId, [
                'level' => 'beginner',
            ]);
        }

        $responseCourses = $this->actingAs($this->regularUser())
            ->get(route('topics.get_knowledge_areas', $topicId))
            ->assertOk()->original;

        $this->assertCount(4, $responseCourses);
    }

    public function test_store(): void
    {
        //        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->regularUser())->post(
            route('topics.store'),
            [
                'name' => 'test topic name',
            ],
        );

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

        $this->actingAs($this->regularUser())->patch(route('topics.update', $topicId), [
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

        $this->actingAs($this->regularUser())->delete(route('topics.destroy', $topicIdToDelete));

        $this->assertCount(1, Topic::all());
    }

    protected function setUp(): void
    {
        parent::setUp();

        GraphModel::runQueryInTransaction('MATCH (n) DETACH DELETE n');
    }
}
