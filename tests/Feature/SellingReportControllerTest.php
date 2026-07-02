<?php

namespace Tests\Feature;

use App\Jobs\GenerateSellingReport;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SellingReportControllerTest extends TestCase
{
    public function test_it_dispatches_the_generation_job_when_the_form_is_submitted(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $user = User::findOrFail($user->id);

        $response = $this->actingAs($user)->post(route('selling_reports.store'), [
            'name' => 'June sales report',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
        ]);

        $response->assertRedirect();

        Queue::assertPushed(GenerateSellingReport::class, function (GenerateSellingReport $job) use ($user): bool {
            return $job->requestData['name'] === 'June sales report'
                && $job->requestData['start_date'] === '2026-06-01'
                && $job->requestData['end_date'] === '2026-06-30'
                && $job->userId === $user->id;
        });
    }
}
