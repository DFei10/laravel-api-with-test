<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TeacherTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_students_are_not_viewable_as_teachers(): void
    {
        $student = Profile::factory()->create()->user;

        $accessToken = $student->createToken('test')->plainTextToken;

        $this->withToken($accessToken)
            ->getJson('/api/teacher/'.$student->id)
            ->assertNotFound();
    }

    public function test_users_can_see_teachers_profiles(): void
    {
        $teacher = User::factory()->create(['category' => 1]);
        Profile::factory()->create(['user_id' => $teacher]);
        Certificate::factory()->create(['user_id' => $teacher]);

        $accessToken = $teacher->createToken('test')->plainTextToken;

        $this->withToken($accessToken)
            ->getJson('/api/teacher/'.$teacher->id)
            ->assertOk()
            ->assertJsonStructure(['profile', 'certificates']);
    }

    public function test_users_can_upgrate_to_become_teachers(): void
    {
        $student = Profile::factory()->create()->user;

        $accessToken = $student->createToken('test')->plainTextToken;
        $response = $this->withToken($accessToken)
            ->postJson(
                '/api/become-teacher',
                [
                    'certificates' => [
                        [
                            'title' => $title = $this->faker->sentence(rand(2, 5)),
                            'university' => $university = $this->faker->sentence(rand(2, 5)),
                            'graduation_date' => $graduation_date = $this->faker->date(),
                        ],
                    ],
                ]
            );

        $response->assertOk()
            ->assertJsonPath('category', 1)
            ->assertJsonPath('certificates.0.title', $title)
            ->assertJsonPath('certificates.0.university', $university)
            ->assertJsonPath('certificates.0.graduation_date', $graduation_date);
    }

    public function test_teachers_can_have_many_certificates(): void
    {
        $student = Profile::factory()->create()->user;

        $accessToken = $student->createToken('test')->plainTextToken;

        $firstCertificate = collect(Certificate::factory()->make())->except('user_id')->toArray();
        $secondCertificate = collect(Certificate::factory()->make())->except('user_id')->toArray();

        $response = $this->withToken($accessToken)->postJson('/api/become-teacher', [
            'certificates' => [
                $firstCertificate,
                $secondCertificate,
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('certificates.0.title', $firstCertificate['title'])
            ->assertJsonPath('certificates.0.university', $firstCertificate['university'])
            ->assertJsonPath('certificates.0.graduation_date', $firstCertificate['graduation_date'])
            ->assertJsonPath('certificates.1.title', $secondCertificate['title'])
            ->assertJsonPath('certificates.1.university', $secondCertificate['university'])
            ->assertJsonPath('certificates.1.graduation_date', $secondCertificate['graduation_date']);
    }
}
