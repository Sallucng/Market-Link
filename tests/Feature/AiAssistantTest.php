<?php

namespace Tests\Feature;

use App\Models\Market;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiAssistantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test AI Assistant endpoint returns valid response.
     */
    public function test_ai_assistant_empty_prompt()
    {
        $response = $this->postJson(route('ai.assistant'), [
            'message' => ''
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply']);
        $this->assertStringContainsString('MarketLink AI Assistant', $response->json('reply'));
    }

    /**
     * Test AI Assistant responds to questions about payment and markets.
     */
    public function test_ai_assistant_handles_questions()
    {
        $response = $this->postJson(route('ai.assistant'), [
            'message' => 'How do I pay for pre-orders and what markets exist?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply', 'source']);
        $this->assertNotEmpty($response->json('reply'));
        // Either gemini responded or fallback responded, both know about in-person pickup settlement
        $this->assertTrue(
            str_contains(strtolower($response->json('reply')), 'person') ||
            str_contains(strtolower($response->json('reply')), 'stall') ||
            str_contains(strtolower($response->json('reply')), 'pickup')
        );
    }
}
