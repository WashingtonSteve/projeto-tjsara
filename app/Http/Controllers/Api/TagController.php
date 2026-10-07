<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Tag\UseCases\CreateTag;
use App\Application\Tag\UseCases\ListTags;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Resources\TagResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class TagController extends Controller
{
    public function __construct(
        private readonly ListTags $listTags,
        private readonly CreateTag $createTag,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return TagResource::collection(($this->listTags)());
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = ($this->createTag)($request->string('name')->toString());

        return TagResource::make($tag)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
