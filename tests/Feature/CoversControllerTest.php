<?php

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

class CoversControllerTest extends TestCase
{
    use WithFaker;

    public function createCovers(?string $coverageLevel = 'beginner'): string
    {
        $courseId = $this->createCourses(1)[0];
        $topicId = $this->createTopics(1)[0];

        return Covers::create($courseId, $topicId, [
            'coverage_level' => $coverageLevel,
        ]);
    }

    public function test_store(): void
    {
        $this->withoutExceptionHandling();

        $courseId = $this->createCourses(1)[0];
        $topicId = $this->createTopics(1)[0];

        $response = $this->post(route('covers.store'), [
            'course_id' => $courseId,
            'topic_id' => $topicId,
            'coverage_level' => 'intermediate',
        ]);

        $allCovers = Covers::all();

        $this->assertCount(1, $allCovers);
        $this->assertEquals('intermediate', $allCovers[0]['coverage_level']);
        $response->assertSessionHas('data', function (array $value) use (
            $allCovers,
        ) {
            return $value['id'] === $allCovers[0]['id'];
        });
    }

    public function test_update(): void
    {
        $this->withoutExceptionHandling();

        $coversId = $this->createCovers();

        $this->patch(route('covers.update', $coversId), [
            'coverage_level' => 'new coverage level',
        ]);

        $this->assertEquals(
            'new coverage level',
            Covers::find($coversId)['coverage_level'],
        );
    }

    public function test_delete(): void
    {
        $this->withoutExceptionHandling();

        $coversIdToDelete = $this->createCovers();
        $this->createCovers();

        $this->assertCount(2, Covers::all());

        $this->delete(route('covers.destroy', $coversIdToDelete));

        $this->assertCount(1, Covers::all());
    }

    protected function setUp(): void
    {
        parent::setUp();

        GraphModel::runQueryInTransaction('MATCH (n) DETACH DELETE n');
    }
}
