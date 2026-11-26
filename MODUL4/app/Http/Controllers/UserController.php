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

    public function index()
{
    $users = User::with('media')->withCount('media')->paginate(10);

    return UserResource::collection($users);
}

    public function show(User $user)
    {
        $user->load('media')->loadCount('media');

        return new UserResource($user);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function collection(User $user)
    {
        return MediaResource::collection($user->media()->orderBy('title')->get());
    }

    public function addToCollection(AddToCollectionRequest $request, User $user)
    {
        $mediaId = $request->validated()['media_id'];

        if ($user->media()->where('media.id', $mediaId)->exists()) {
            return response()->json(['message' => 'Media sudah ada di koleksi'], 409);
        }

        $user->media()->attach($mediaId);

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi',
            'data' => new MediaResource(Media::find($mediaId)),
        ], 201);
    }

    public function removeFromCollection(User $user, Media $media)
    {
        if (! $user->media()->where('media.id', $media->id)->exists()) {
            return response()->json(['message' => 'Media tidak ada di koleksi pengguna'], 404);
        }

        $user->media()->detach($media->id);

        return response()->json(['message' => 'Media berhasil dihapus dari koleksi']);
    }
}
