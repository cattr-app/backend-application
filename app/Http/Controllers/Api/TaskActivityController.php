<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskActivity\ShowTaskActivityRequest;
use App\Models\TaskComment;
use App\Models\TaskHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TaskActivityController extends Controller
{
    protected $taskComment;
    protected $taskHistory;

    public function __construct(TaskComment $taskComment, TaskHistory $taskHistory)
    {
        $this->taskComment = $taskComment;
        $this->taskHistory = $taskHistory;
    }

    public function getCollection(string $type,int $taskId): array
    {
        $comments = $this->taskComment->where('task_id', $taskId)->with('user')->get()->toArray();
        $history = $this->taskHistory->where('task_id', $taskId)->with('user')->get()->toArray();
        $result = [];
        
        if( $type === "all" ) {
            $result = array_merge($comments, $history);
        } else if ( $type === "history" ) {
            $result = $history;
        } else if ($type === "comments") {
            $result = $comments;
        }

        foreach ($result as &$item) {
            $item['can_change'] = $item['user_id'] === Auth::id();
        }
        return $result;
    }

    public function sortCollection(array &$collection, string $sort): void
    {
        usort($collection, function ($a, $b) use ($sort) {
            if ($sort === "desc")
                return strtotime($a['created_at']) < strtotime($b['created_at']);
            else
                return strtotime($a['created_at']) > strtotime($b['created_at']);
        });
    }

    public function getPaginateCollection(array $collection, int $currentPage, $perPage = 10): LengthAwarePaginator
    {
        $collection = new Collection($collection);

        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        return new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
    }

    public function index(ShowTaskActivityRequest $request): JsonResponse
    {
        $searchResults = $this->getCollection($request->get('type'), $request->get('task_id'));

        $this->sortCollection($searchResults, $request->get('sort'));

        $paginatedSearchResults = $this->getPaginateCollection($searchResults, $request->get('page'));

        return responder()->success($paginatedSearchResults)->respond();
    }
}
