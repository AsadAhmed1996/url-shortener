<?php

namespace Tests\Feature;

use App\Models\ShortUrl;
use App\Services\ShortUrlService;
use Illuminate\Support\Str;
use Tests\TestCase;

class EncodeUrlTest extends TestCase
{
    private const MISFORMATTED_URL = 'testcom';
    private const EXISTING_ORIGINAL_URL = 'http://test.com/testingthislongurlthatiwantoshorten?testing=1';
    private const EXISTING_ORIGINAL_URL_SHORT_URL = 'http://test.com/7aWhti';
    private const ENCODE_ENDPOINT = '/api/encode';

    public function setUp(): void
    {
        parent::setUp();

        ShortUrl::where('original_url', self::EXISTING_ORIGINAL_URL)->delete();
        ShortUrl::create([
            'original_url' => self::EXISTING_ORIGINAL_URL,
            'short_url' => self::EXISTING_ORIGINAL_URL_SHORT_URL,
        ]);
    }

    public function test_that_missing_url_triggers_validation_error_when_encoding(): void
    {
        $this->post(self::ENCODE_ENDPOINT, [])->assertInvalid(['url']);
    }

    public function test_that_misformatted_url_triggers_validation_error_when_encoding(): void
    {
        $this->post(self::ENCODE_ENDPOINT, ['url' => self::MISFORMATTED_URL])->assertInvalid(['url']);
    }

    public function test_that_url_which_is_too_short_to_shorten_triggers_validation_error_when_encoding(): void
    {
        $shortUrlService = new ShortUrlService();
        $minLength = $shortUrlService->getMinUrlLength();
        $randomLength = $minLength - strlen('http://.com') - 1;

        $this->post(self::ENCODE_ENDPOINT, [
            'url' => 'http://'.Str::random($randomLength).'.com'
        ])->assertInvalid(['url']);
    }

    public function test_that_encoding_valid_existing_url_returns_saved_short_url_in_json_format_without_errors(): void
    {
        $response = $this->post(
            self::ENCODE_ENDPOINT, ['url' => self::EXISTING_ORIGINAL_URL]
        )->assertSessionDoesntHaveErrors()->content();

        $this->assertJson($response);
        $this->assertEquals(
            self::EXISTING_ORIGINAL_URL_SHORT_URL,
            json_decode($response)->short_url
        );
    }

    public function test_that_encoding_valid_new_url_saves_new_entry_in_db_and_returns_a_unique_short_url_in_json_format_without_errors(): void
    {
        $originalUrl = 'http://testing-a-new-url.com/thisisreallyquitelongisntitohwellwecanshorten?letsshorten=yesplease';

        $response = $this->post(
            self::ENCODE_ENDPOINT, ['url' => $originalUrl]
        )->assertSessionDoesntHaveErrors()->content();
        $this->assertJson($response);

        $shortUrl = json_decode($response)->short_url;

        $urlRecord = ShortUrl::where('original_url', $originalUrl)
            ->where('short_url', $shortUrl)
            ->first();
        $this->assertNotNull($urlRecord);

        $shortUrlCount = ShortUrl::where('short_url', $shortUrl)->count();
        $this->assertEquals(1, $shortUrlCount);

        ShortUrl::where('original_url', $originalUrl)->delete();
    }
}
