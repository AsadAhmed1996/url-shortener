<?php

namespace App\Services;

use App\Exceptions\ShortUrlGenerationException;
use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Illuminate\Support\Str;

class ShortUrlService extends Controller
{
    private const SHORT_URL_PREFIX = 'http://short.ened/';
    private const PATH_LENGTH = 6;
    private const MINIMUM_REDUCED_CHARACTERS = 10;

    /**
     * Get shortened url from database.
     *
     * @param string $originalUrl
     * @return string|null
     */
    public function getShortUrl(string $originalUrl): string|null
    {
        $urlData = ShortUrl::where('original_url', $originalUrl)->first();
        return $urlData?->short_url;
    }

    /**
     * Get original url from database.
     *
     * @param string $shortUrl
     * @return string|null
     */
    public function getOriginalUrl(string $shortUrl): string|null
    {
        $urlData = ShortUrl::where('short_url', $shortUrl)->first();
        return $urlData?->original_url;
    }

    /**
     * Create a unique short url not already present in database.
     *
     * @return string
     * @throws ShortUrlGenerationException
     */
    public function createUniqueShortUrl(): string
    {
        $uniqueUrlCreated = false;
        $attempt = 0;
        $maxAttempts = 50;

        while (!$uniqueUrlCreated && $attempt < $maxAttempts) {
            $shortUrl = self::SHORT_URL_PREFIX . Str::random(self::PATH_LENGTH);
            $uniqueUrlCreated = !$this->getOriginalUrl($shortUrl);
            $attempt++;
        }

        if (!$uniqueUrlCreated) {
            throw new ShortUrlGenerationException('Short URL generation failed. Please try again later.');
        }

        return $shortUrl;
    }

    /**
     * Save new url data to the database.
     *
     * @param string $originalUrl
     * @param string $shortUrl
     * @return void
     */
    public function saveUrlData(string $originalUrl, string $shortUrl): void
    {
        ShortUrl::create([
            'original_url' => $originalUrl,
            'short_url' => $shortUrl
        ]);
    }

    /**
     * Get the minimum amount of characters a url must be to be shortened.
     *
     * @return int
     */
    public function getMinUrlLength(): int
    {
        return strlen(self::SHORT_URL_PREFIX) + self::PATH_LENGTH + self::MINIMUM_REDUCED_CHARACTERS;
    }
}
