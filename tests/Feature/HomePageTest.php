<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_explains_the_current_review_workflow(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('AI Client Intake');
        $response->assertSee('Alur review siap digunakan');
        $response->assertSee('Mulai intake');
        $response->assertSee('Submission reviewer');
        $response->assertSee('Data dari calon klien');
        $response->assertSee('Klarifikasi dan risiko');
        $response->assertSee(route('intake.create'), false);
        $response->assertSee(route('reviewer.submissions.index'), false);
        $response->assertDontSee('Fondasi aplikasi siap dipelajari');
        $response->assertDontSee('Halaman ini belum menerima data calon klien');
        $response->assertDontSee('Review belum berarti approval');
    }

    public function test_home_does_not_offer_unimplemented_decision_actions(): void
    {
        $response = $this->get(route('home'));

        $response->assertDontSee('Approve');
        $response->assertDontSee('Reject');
        $response->assertDontSee('Merge');
        $response->assertDontSee('Discard');
    }
}
