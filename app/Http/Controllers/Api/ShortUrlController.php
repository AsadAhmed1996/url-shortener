<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ShortUrlGenerationException;
use App\Http\Controllers\Controller;
use App\Rules\ShortUrlExists;
use App\Services\ShortUrlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShortUrlController extends Controller
{
    public function __construct(
        private readonly ShortUrlService $shortUrlService
    ) {
    }

    /**
     * Shorten url.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function encode(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'url' => ['required', 'url', 'min:'.$this->shortUrlService->getMinUrlLength()]
        ]);
        $originalUrl = $validatedData['url'];

        try {
            $shortUrl = $this->shortUrlService->getShortUrl($originalUrl);
            if (!$shortUrl) {
                $shortUrl = $this->shortUrlService->createUniqueShortUrl();
                $this->shortUrlService->saveUrlData($originalUrl, $shortUrl);
            }
        } catch (ShortUrlGenerationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => ['short_url_generation' => ['Short URL generation temporarily unavailable.']]
            ]);
        }

        return response()->json(['short_url' => $shortUrl]);
    }

    /**
     * Return original url for short url.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function decode(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'url' => ['required', 'url', new ShortUrlExists()]
        ]);
        $shortUrl = $validatedData['url'];
        $originalUrl = $this->shortUrlService->getOriginalUrl($shortUrl);

        return response()->json(['original_url' => $originalUrl]);
    }
}
