<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserDeletionRequest;
use App\Services\Rbac\UserDeletionApprovalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserDeletionRequestController extends Controller
{
    public function __construct(private readonly UserDeletionApprovalService $approvalService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->get('status', UserDeletionRequest::PENDING);

        $requests = UserDeletionRequest::with(['user.role', 'user.wilayah', 'requester', 'reviewer'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('requested_at')
            ->paginate((int) $request->get('per_page', 25));

        return response()->json($requests);
    }

    public function approve(Request $request, UserDeletionRequest $userDeletionRequest): JsonResponse
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $approved = $this->approvalService->approve(
            $userDeletionRequest,
            $request->user(),
            $validated['note'] ?? null
        );

        return response()->json([
            'message' => 'Permintaan hapus user disetujui.',
            'data' => $approved,
        ]);
    }

    public function reject(Request $request, UserDeletionRequest $userDeletionRequest): JsonResponse
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $rejected = $this->approvalService->reject(
            $userDeletionRequest,
            $request->user(),
            $validated['note'] ?? null
        );

        return response()->json([
            'message' => 'Permintaan hapus user ditolak.',
            'data' => $rejected,
        ]);
    }
}
