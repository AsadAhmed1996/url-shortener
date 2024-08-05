<?php

namespace Tests\Feature;

use App\Models\ShortUrl;
use Tests\TestCase;

class DecodeUrlTest extends TestCase
{
    private const MISFORMATTED_URL = 'testcom';
    private const EXISTING_ORIGINAL_URL = 'http://test.com/testingthislongurlthatiwantoshorten?testing=1';
    private const EXISTING_ORIGINAL_URL_SHORT_URL = 'http://test.com/7aWhti';
    private const DECODE_ENDPOINT = '/api/decode';

    public function setUp(): void
    {
        parent::setUp();

        ShortUrl::where('original_url', self::EXISTING_ORIGINAL_URL)->delete();
        ShortUrl::create([
            'original_url' => self::EXISTING_ORIGINAL_URL,
            'short_url' => self::EXISTING_ORIGINAL_URL_SHORT_URL,
        ]);
    }

    public function test_that_missing_url_triggers_validation_error_when_decoding(): void
    {
        $this->post(self::DECODE_ENDPOINT, [])->assertInvalid(['url']);
    }

    public function test_that_misformatted_url_triggers_validation_error_when_decoding(): void
    {
        $this->post(self::DECODE_ENDPOINT, ['url' => self::MISFORMATTED_URL])->assertInvalid(['url']);
    }

    public function test_that_decoding_a_short_url_not_present_in_database_triggers_validation_error(): void
    {
        $shortUrl = 'http://not-present.com/missing';

        $this->post(self::DECODE_ENDPOINT, ['url' => $shortUrl])->assertInvalid(['url']);
    }

    public function test_that_decoding_valid_existing_short_url_returns_correct_original_url_in_json_format_with_no_errors(): void
    {
        $response = $this->post(
            self::DECODE_ENDPOINT, ['url' => self::EXISTING_ORIGINAL_URL_SHORT_URL]
        )->assertSessionDoesntHaveErrors()->content();
        $this->assertJson($response);

        $this->assertEquals(self::EXISTING_ORIGINAL_URL, json_decode($response)->original_url);
    }
}
