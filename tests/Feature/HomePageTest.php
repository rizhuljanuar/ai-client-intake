<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_displays_the_workflow_status(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('AI Client Intake');
        $response->assertSee('Fondasi aplikasi siap dipelajari');
        $response->assertSee('Menyiapkan model data intake');
    }
}
