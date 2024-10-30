<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DictionaryController extends Controller
{
    // [GET] /entries/en
    public function entries(Request $request, $language)
    {
        $search = $request->input('search');
        $limit = $request->input('limit', 10);
        $cursor = $request->input('cursor');

        if (empty($search)) {
            return response()->json(['error' => 'Insira todos os parâmetros'], 400);
        }

        $cacheKey = "dictionary:entries:{$language}:{$search}";

        $cachedResponse = cache()->get($cacheKey);

        if ($cachedResponse) {
            return response()->json($cachedResponse)
                ->header('x-cache', 'HIT')
                ->header('x-response-time', 0);
        }

        $startTime = microtime(true);

        $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/{$language}/{$search}");

        if ($response->failed()) {
            return response()->json(['error' => 'Nenhum resultado encontrado'], 404);
        }

        $results = $response->json();
        $words = array_map(function ($entry) {
            return $entry['word'];
        }, $results);

        $totalDocs = count($words);

        if ($cursor) {
            $decodedCursor = json_decode(base64_decode($cursor), true);
            $cursorIndex = array_search($decodedCursor['word'], $words);
        } else {
            $cursorIndex = 0;
        }

        $currentPageResults = array_slice($words, $cursorIndex, $limit);

        $nextCursor = null;
        $previousCursor = null;

        if ($cursorIndex + $limit < $totalDocs) {
            $nextWord = $words[$cursorIndex + $limit];
            $nextCursor = base64_encode(json_encode(['word' => $nextWord]));
        }

        if ($cursorIndex > 0) {
            $previousWord = $words[max(0, $cursorIndex - $limit)];
            $previousCursor = base64_encode(json_encode(['word' => $previousWord]));
        }

        $responseData = [
            'results' => $currentPageResults,
            'totalDocs' => $totalDocs,
            'previous' => $previousCursor,
            'next' => $nextCursor,
            'hasNext' => isset($nextCursor),
            'hasPrev' => isset($previousCursor),
        ];

        cache()->put($cacheKey, $responseData, 1800); // 30 min

        $responseTime = (microtime(true) - $startTime) * 1000;

        return response()->json($responseData)
            ->header('x-cache', 'MISS')
            ->header('x-response-time', $responseTime);
    }

    // [GET] /entries/en/:word
    public function show(Request $request, $language, $word)
    {
        $cacheKey = "dictionary:show:{$language}:{$word}";

        $cachedResponse = cache()->get($cacheKey);

        if ($cachedResponse) {
            return response()->json($cachedResponse)
                ->header('x-cache', 'HIT')
                ->header('x-response-time', 0);
        }

        $startTime = microtime(true);

        $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/{$language}/{$word}");

        if ($response->failed()) {
            return response()->json(['error' => 'Palavra não encontrada'], 404);
        }

        $responseData = $response->json();

        $user = Auth::user();

        if ($user) {
            History::create([
                'user_id' => $user->id,
                'word' => $word,
            ]);
        }

        cache()->put($cacheKey, $responseData, 1800);  // 30 min

        $responseTime = (microtime(true) - $startTime) * 1000;

        return response()->json($responseData)
            ->header('x-cache', 'MISS')
            ->header('x-response-time', $responseTime);
    }

    // [POST] /entries/en/:word/favorite
    public function addFavorite(Request $request, $language, $word)
    {
        $user = Auth::user();

        if (Favorite::where('user_id', $user->id)->where('word', $word)->exists()) {
            return response()->json(['message' => 'Palavra já nos favoritos'], 400);
        }

        Favorite::create([
            'user_id' => $user->id,
            'word' => $word,
        ]);

        return response()->json(null, 204);
    }

    // [DELETE] /entries/en/:word/unfavorite
    public function removeFavorite(Request $request, $language, $word)
    {
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)->where('word', $word);
        if (! $favorite->exists()) {
            return response()->json(['message' => 'Palavra já nos favoritos'], 400);
        }

        $favorite->delete();

        return response()->json(null, 204);
    }

    // [GET] /user/me
    public function userProfile()
    {
        return response()->json(Auth::user());
    }

    // [GET] /user/me/history
    public function userHistory(Request $request)
    {
        $user = Auth::user();
        $history = History::where('user_id', $user->id)->get();

        return response()->json([
            'results' => $history,
            'totalDocs' => $history->count(),
        ]);
    }

    // [GET] /user/me/favorites
    public function userFavorites(Request $request)
    {
        $user = Auth::user();
        $favorites = Favorite::where('user_id', $user->id)->get();

        return response()->json([
            'results' => $favorites,
            'totalDocs' => $favorites->count(),
        ]);
    }
}
