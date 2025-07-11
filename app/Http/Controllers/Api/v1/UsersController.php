<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\GetByIdsRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function getByIds(GetByIdsRequest $request): JsonResponse
    {
        $users = User::query()
                ->select('id', 'name')
                ->whereNull('deleted_at')
                ->whereIn('id', $request->ids)
                ->get();

        return response()->json([
            'users' => $users
        ], 200);
    }

    public function search(Request $request): JsonResponse
    {
        // 
    }
}
