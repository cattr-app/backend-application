<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Status\DestroyStatusRequest;
use App\Http\Requests\TaskComment\CreateTaskCommentRequest;
use App\Http\Requests\TaskComment\DestroyTaskCommentRequest;
use App\Http\Requests\TaskComment\ListTaskCommentRequest;
use App\Http\Requests\TaskComment\ShowTaskCommentRequestStatus;
use App\Http\Requests\TaskComment\UpdateTaskCommentRequest;
use Filter;
use App\Models\TaskComment;
use App\Models\TaskHistory;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskActivityController
{
    protected $taskComment;
    protected $taskHistory;

    public function __construct(TaskComment $taskComment,TaskHistory $taskHistory)
    {
        $this->taskComment = $taskComment;
        $this->taskHistory = $taskHistory;
    }
    public function getCollection(string $type,int $taskId) {
        $comments = $this->taskComment->where('task_id', $taskId)->with('user')->get()->toArray();
        $history = $this->taskHistory->where('task_id', $taskId)->with('user')->get()->toArray();

        if( $type === "all" ) {
            return array_merge($comments, $history);
        } else if ( $type === "history" ) {
            return $history;
        } else if ($type === "comments") {
            return $comments;
        }
    }
    public function sortCollection(array &$collection, string $sort)
    {
        usort($collection, function ($a, $b) use ($sort) {
            if ($sort === "desc")
                return strtotime($a['created_at']) < strtotime($b['created_at']);
            else
                return strtotime($a['created_at']) > strtotime($b['created_at']);
        });
    }
    public function index(Request $request)
    {
        $currentPage = $request->get('page');

        $searchResults = $this->getCollection($request->get('type'), $request->get('task_id'));

        $this->sortCollection($searchResults, $request->get('sort'));

        $collection = new Collection($searchResults);

        $perPage = 10;

        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedSearchResults = new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
        
        return responder()->success($paginatedSearchResults)->respond();
    }
}
