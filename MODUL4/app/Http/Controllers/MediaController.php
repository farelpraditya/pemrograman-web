<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * GET /api/media?search=judul&category=Anime
     */
    public function index(Request $request)
    {
        $media = Media::query()
            ->withCount('users')
            ->search($request->query('search'))
            ->ofCategory($request->query('category'))
            ->orderBy('title')
            ->paginate(10);

        return MediaResource::collection($media);
    }

    /**
     * GET /api/media/{media}  -> detail media beserta pengguna yang memasukkannya ke koleksi
     */
    public function show(Media $media)
    {
        $media->load('users')->loadCount('users');

        return new MediaResource($media);
    }

    /**
     * POST /api/media
     */
    public function store(StoreMediaRequest $request)
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))->response()->setStatusCode(201);
    }

    /**
     * PATCH /api/media/{media}
     */
    public function update(StoreMediaRequest $request, Media $media)
    {
        $media->update($request->validated());

        return new MediaResource($media);
    }

    /**
     * DELETE /api/media/{media}
     */
    public function destroy(Media $media)
    {
        $media->delete();

        return response()->json(['message' => 'Media berhasil dihapus']);
    }
}
