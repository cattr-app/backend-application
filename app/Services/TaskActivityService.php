<?php
namespace App\Services;

use App\Models\TaskComment;
use App\Models\TaskHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TaskActivityService
{
    protected $taskComment;
    protected $taskHistory;

    public function __construct(TaskComment $taskComment, TaskHistory $taskHistory)
    {
        $this->taskComment = $taskComment;
        $this->taskHistory = $taskHistory;
    }

    private function _getCollection(string $type, int $taskId): array
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

        $result = array_map(function($item){
            $item['can_change'] = $item['user_id'] === Auth::id();
            return $item;
        },$result);

        return $result;
    }

    private function _sortCollection(array &$collection, string $sort): void
    {
        usort($collection, function ($a, $b) use ($sort) {
            if ($sort === "desc")
                return strtotime($a['created_at']) < strtotime($b['created_at']);
            else
                return strtotime($a['created_at']) > strtotime($b['created_at']);
        });
    }

    private function _getPaginateCollection(array $collection, int $currentPage, $perPage = 10): LengthAwarePaginator
    {
        $collection = new Collection($collection);

        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        return new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
    }

    public function getActivity(array $filter): LengthAwarePaginator
    {
        $searchResults = $this->_getCollection($filter['type'], $filter['task_id']);

        $this->_sortCollection($searchResults, $filter['sort']);

        $paginatedSearchResults = $this->_getPaginateCollection($searchResults, $filter['page']);

        return $paginatedSearchResults;
    }
}
