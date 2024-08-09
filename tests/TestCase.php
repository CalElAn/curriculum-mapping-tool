<?php

namespace Tests;

use App\GraphModels\Course;
use App\GraphModels\KnowledgeArea;
use App\GraphModels\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TopicControllerTest;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;

    //    public static User $adminUser;
    //    public static User $regularUser;
    //
    //    public static function setUpBeforeClass(): void
    //    {
    //        self::$adminUser = User::factory()->create();
    //        self::$regularUser = User::factory()->create();
    //
    //        Role::create(['name' => 'admin']);
    //
    //        self::$adminUser->assignRole('admin');
    //    }

    public function adminUser(): User
    {
        $adminUser = User::factory()->create();

        Role::create(['name' => 'admin']);

        $adminUser->assignRole('admin');

        return $adminUser;
    }

    public function regularUser(): User
    {
        return User::factory()->create();
    }

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

    public function createKnowledgeAreas(int $numberToCreate): array
    {
        $titles = $this->faker->words($numberToCreate);
        $descriptions = $this->faker->paragraphs($numberToCreate);

        $ids = [];

        foreach ($titles as $index => $title) {
            $ids[] = KnowledgeArea::create([
                'title' => $title,
                'description' => $descriptions[$index],
            ]);
        }

        return $ids;
    }
}
