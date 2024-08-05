<?php

namespace Tests\Unit;

use App\Exceptions\ShortUrlGenerationException;
use App\Models\ShortUrl;
use App\Services\ShortUrlService;
use Tests\TestCase;

class ShortUrlServiceTest extends TestCase
{
    private const EXISTING_ORIGINAL_URL = 'http://test.com/testingthislongurlthatiwantoshorten?testing=1';
    private const EXISTING_ORIGINAL_URL_SHORT_URL = 'http://test.com/7aWhti';

    public function setUp(): void
    {
        parent::setUp();

        $this->shortUrlService = new ShortUrlService();

        ShortUrl::where('original_url', self::EXISTING_ORIGINAL_URL)->delete();
        ShortUrl::create([
            'original_url' => self::EXISTING_ORIGINAL_URL,
            'short_url' => self::EXISTING_ORIGINAL_URL_SHORT_URL,
        ]);
    }

    public function test_that_get_short_url_for_missing_original_url_returns_null(): void
    {
        $shortUrl = $this->shortUrlService->getShortUrl('http://test.com/testing');
        $this->assertNull($shortUrl);
    }

    public function test_that_get_short_url_for_existing_original_url_returns_correct_short_url(): void
    {
        $shortUrl = $this->shortUrlService->getShortUrl(self::EXISTING_ORIGINAL_URL);
        $this->assertEquals(self::EXISTING_ORIGINAL_URL_SHORT_URL, $shortUrl);
    }

    public function test_that_get_original_url_for_missing_short_url_returns_null(): void
    {
        $shortUrl = $this->shortUrlService->getOriginalUrl('http://test.com/testing');
        $this->assertNull($shortUrl);
    }

    public function test_that_get_original_url_for_existing_short_url_returns_correct_original_url(): void
    {
        $shortUrl = $this->shortUrlService->getOriginalUrl(self::EXISTING_ORIGINAL_URL_SHORT_URL);
        $this->assertEquals(self::EXISTING_ORIGINAL_URL, $shortUrl);
    }

    public function test_that_create_unique_short_url_returns_a_unique_short_url(): void
    {
        $shortUrl = $this->shortUrlService->createUniqueShortUrl();

        $count = ShortUrl::where('short_url', $shortUrl)->count();
        $this->assertEquals(0, $count);
    }

    /**
     * @throws ShortUrlGenerationException
     */
    public function test_that_create_unique_short_url_throws_custom_exception_if_too_many_attempts(): void
    {
        $mockShortUrlService = $this->getMockBuilder(ShortUrlService::class)
            ->onlyMethods(['getOriginalUrl'])
            ->getMock();
        $mockShortUrlService->method('getOriginalUrl')
            ->willReturn('existing-url');

        $this->expectException(ShortUrlGenerationException::class);

        $mockShortUrlService->createUniqueShortUrl();
    }

    public function test_that_save_url_data_saves_data_to_db(): void
    {
        $originalUrl = 'http://another-url-to-shorten.com/lets-hope-it-saves';
        $shortUrl = 'http://the-short-one/short';
        $this->shortUrlService->saveUrlData($originalUrl, $shortUrl);

        $urlData = ShortUrl::where('original_url', $originalUrl)->first();

        ShortUrl::where('original_url', $originalUrl)->delete();

        $this->assertNotNull($urlData);

        $this->assertEquals($shortUrl, $urlData->short_url);
    }
}
