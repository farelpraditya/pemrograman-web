<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCollectionRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;

class UserController extends Controller
{
    /**
     * GET /api/users
     */
    public function index()
    {
        $users = User::withCount('media')->paginate(10);

        return UserResource::collection($users);
    }

    /**
     * GET /api/users/{user}  -> detail pengguna beserta koleksi medianya
     */
    public function show(User $user)
    {
        $user->load('media')->loadCount('media');

        return new UserResource($user);
    }

    /**
     * POST /api/users
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    // ================= CUSTOM ACTION (mengubah relasi / pivot) =================

    /**
     * GET /api/users/{user}/collection  -> daftar koleksi media milik pengguna
     */
    public function collection(User $user)
    {
        return MediaResource::collection($user->media()->orderBy('title')->get());
    }

    /**
     * POST /api/users/{user}/collection  body: { "media_id": 1 }
     */
    public function addToCollection(AddToCollectionRequest $request, User $user)
    {
        $mediaId = $request->validated()['media_id'];

        // Cegah data duplikat -> 409 Conflict
        if ($user->media()->where('media.id', $mediaId)->exists()) {
            return response()->json(['message' => 'Media sudah ada di koleksi'], 409);
        }

        $user->media()->attach($mediaId);

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi',
            'data' => new MediaResource(Media::find($mediaId)),
        ], 201);
    }

    /**
     * DELETE /api/users/{user}/collection/{media}
     */
    public function removeFromCollection(User $user, Media $media)
    {
        if (! $user->media()->where('media.id', $media->id)->exists()) {
            return response()->json(['message' => 'Media tidak ada di koleksi pengguna'], 404);
        }

        $user->media()->detach($media->id);

        return response()->json(['message' => 'Media berhasil dihapus dari koleksi']);
    }
}
