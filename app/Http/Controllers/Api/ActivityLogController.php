<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ActivityLogController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ActivityLogResource::collection(
            ActivityLog::query()->latest('id')->limit(20)->get()
        );
    }
}
