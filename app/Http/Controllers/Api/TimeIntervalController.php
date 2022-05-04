<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ScreenshotService;
use App\Http\Requests\TimeInterval\BulkDestroyTimeIntervalRequest;
use App\Http\Requests\TimeInterval\BulkEditTimeIntervalRequest;
use App\Http\Requests\TimeInterval\CreateTimeIntervalRequest;
use App\Http\Requests\TimeInterval\DestroyTimeIntervalRequest;
use App\Http\Requests\TimeInterval\EditTimeIntervalRequest;
use App\Http\Requests\TimeInterval\ListTimeIntervalRequest;
use App\Http\Requests\TimeInterval\PutScreenshotRequest;
use App\Http\Requests\TimeInterval\ScreenshotRequest;
use App\Http\Requests\TimeInterval\ShowTimeIntervalRequest;
use App\Http\Requests\TimeInterval\TrackAppRequest;
use App\Jobs\AssignAppsToTimeInterval;
use App\Models\TrackedApplication;
use App\Models\User;
use Event;
use Filter;
use App\Models\TimeInterval;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Settings;
use Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Validator;

class TimeIntervalController extends ItemController
{
    protected const MODEL = TimeInterval::class;

    public function __construct(protected ScreenshotService $screenshotService)
    {
    }

    /**
     * @param ListTimeIntervalRequest $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(ListTimeIntervalRequest $request): JsonResponse
    {
        Filter::listen(Filter::getRequestFilterName(), static function ($filters) use ($request) {
            if ($request->get('project_id')) {
                $filters['task.project_id'] = $request->get('project_id');
            }

            return $filters;
        });

        return $this->_index($request);
    }

    /**
     * @api             {post} /time-intervals/create Create
     * @apiDescription  Create Time Interval
     *
     * @apiVersion      1.0.0
     * @apiName         Create
     * @apiGroup        Time Interval
     *
     * @apiUse          AuthHeader
     *
     * @apiPermission   time_intervals_create
     * @apiPermission   time_intervals_full_access
     *
     * @apiParam {Integer}  task_id           Task id
     * @apiParam {Integer}  user_id           User id
     * @apiParam {String}   start_at          Interval time start
     * @apiParam {String}   end_at            Interval time end
     *
     * @apiParam {Integer}  [activity_fill]   Activity rate as a percentage
     * @apiParam {Integer}  [mouse_fill]      Time spent using the mouse as a percentage
     * @apiParam {Integer}  [keyboard_fill]   Time spent using the keyboard as a percentage
     *
     * @apiParamExample {json} Request Example
     * {
     *   "task_id": 1,
     *   "user_id": 1,
     *   "start_at": "2013-04-12T16:40:00-04:00",
     *   "end_at": "2013-04-12T16:40:00-04:00"
     * }
     *
     * @apiSuccess {Object}   interval  Interval
     *
     * @apiUse          TimeIntervalObject
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "interval": {
     *      "id": 2251,
     *      "task_id": 1,
     *      "start_at": "2013-04-12 20:40:00",
     *      "end_at": "2013-04-12 20:40:00",
     *      "is_manual": true,
     *      "created_at": "2018-10-01 03:20:59",
     *      "updated_at": "2018-10-01 03:20:59",
     *      "activity_fill": 0,
     *      "mouse_fill": 0,
     *      "keyboard_fill": 0,
     *      "user_id": 1
     *    }
     *  }
     *
     * @apiUse          400Error
     * @apiUse          ValidationError
     * @apiUse          UnauthorizedError
     * @apiUse          ForbiddenError
     */

    /**
     * @param ShowTimeIntervalRequest $request
     *
     * @return JsonResponse
     * @throws Throwable
     * @api             {post} /time-intervals/show Show
     * @apiDescription  Show Time Interval
     *
     * @apiVersion      1.0.0
     * @apiName         Show
     * @apiGroup        Time Interval
     *
     * @apiUse          AuthHeader
     *
     * @apiPermission   time_intervals_show
     * @apiPermission   time_intervals_full_access
     *
     * @apiParam {Integer}  id     Time Interval id
     *
     * @apiUse          TimeIntervalParams
     *
     * @apiParamExample {json} Request Example
     * {
     *   "id": 1
     * }
     *
     * @apiUse          TimeIntervalObject
     *
     * @apiSuccessExample {json} Response Example
     * {
     *   "id": 1,
     *   "task_id": 1,
     *   "start_at": "2006-05-31 16:15:09",
     *   "end_at": "2006-05-31 16:20:07",
     *   "created_at": "2018-09-25 06:15:08",
     *   "updated_at": "2018-09-25 06:15:08",
     *   "deleted_at": null,
     *   "activity_fill": 42,
     *   "mouse_fill": 43,
     *   "keyboard_fill": 43,
     *   "user_id": 1
     * }
     *
     * @apiUse          400Error
     * @apiUse          UnauthorizedError
     * @apiUse          ItemNotFoundError
     * @apiUse          ForbiddenError
     * @apiUse          ValidationError
     */
    public function show(ShowTimeIntervalRequest $request): JsonResponse
    {
        return $this->_show($request);
    }

    /**
     * @apiDeprecated   since 1.0.0
     * @api             {post} /time-intervals/bulk-create Bulk Create
     * @apiDescription  Create Time Intervals
     *
     * @apiVersion      1.0.0
     * @apiName         Bulk Create
     * @apiGroup        Time Interval
     */

    /**
     * @param EditTimeIntervalRequest $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit(EditTimeIntervalRequest $request): JsonResponse
    {
        Filter::listen(Filter::getRequestFilterName(), static function ($requestData) {
            $requestData['start_at'] = Carbon::parse($requestData['start_at'])->setTimezone('UTC')->toDateTimeString();
            $requestData['end_at'] = Carbon::parse($requestData['end_at'])->setTimezone('UTC')->toDateTimeString();
        });

        return $this->_edit($request);
    }

    /**
     * @api             {post} /time-intervals/list List
     * @apiDescription  Get list of Time Intervals
     *
     * @apiVersion      1.0.0
     * @apiName         List
     * @apiGroup        Time Interval
     *
     * @apiUse          AuthHeader
     *
     * @apiPermission   time_intervals_list
     * @apiPermission   time_intervals_full_access
     *
     * @apiUse          TimeIntervalParams
     * @apiUse          TimeIntervalObject
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  [
     *    {
     *      "id": 1,
     *      "task_id": 1,
     *      "start_at": "2006-06-20 15:54:40",
     *      "end_at": "2006-06-20 15:59:38",
     *      "created_at": "2018-10-15 05:54:39",
     *      "updated_at": "2018-10-15 05:54:39",
     *      "deleted_at": null,
     *      "activity_fill": 42,
     *      "mouse_fill": 43,
     *      "keyboard_fill": 43,
     *      "user_id":1
     *    }
     *  ]
     *
     * @apiUse          400Error
     * @apiUse          UnauthorizedError
     * @apiUse          ForbiddenError
     */

    /**
     * @throws Exception
     * @api             {get,post} /time-intervals/count Count
     * @apiDescription  Count Time Intervals
     *
     * @apiVersion      1.0.0
     * @apiName         Count
     * @apiGroup        Time Interval
     *
     * @apiUse          AuthHeader
     *
     * @apiSuccess {String}   total    Amount of users that we have
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "total": 2
     *  }
     *
     * @apiUse          400Error
     * @apiUse          ForbiddenError
     * @apiUse          UnauthorizedError
     */
    public function count(ListTimeIntervalRequest $request): JsonResponse
    {
        return $this->_count($request);
    }

    /**
     * @api             {post} /time-intervals/edit Edit
     * @apiDescription  Edit Time Interval
     *
     * @apiVersion      1.0.0
     * @apiName         Edit
     * @apiGroup        Time Interval
     *
     * @apiUse          AuthHeader
     *
     * @apiPermission   time_intervals_edit
     * @apiPermission   time_intervals_full_access
     *
     * @apiParam {Integer}  id           Time Interval id
     *
     * @apiUse          TimeIntervalParams
     *
     * @apiSuccess {Object}   res      TimeInterval
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "res": {
     *      "id":1,
     *      "task_id":1,
     *      "start_at":"2018-10-03 10:00:00",
     *      "end_at":"2018-10-03 10:00:00",
     *      "created_at":"2018-10-15 05:50:39",
     *      "updated_at":"2018-10-15 05:50:43",
     *      "deleted_at":null,
     *      "activity_fill": 42,
     *      "mouse_fill": 43,
     *      "keyboard_fill": 43,
     *      "user_id":1
     *    }
     *  }
     *
     * @apiUse          400Error
     * @apiUse          ValidationError
     * @apiUse          UnauthorizedError
     * @apiUse          ItemNotFoundError
     */

    /**
     * @api             {post} /time-intervals/bulk-edit Bulk Edit
     * @apiDescription  Multiple Edit TimeInterval to assign tasks to them
     *
     * @apiVersion      1.0.0
     * @apiName         Bulk Edit
     * @apiGroup        Time Interval
     *
     * @apiUse          AuthHeader
     *
     * @apiParam {Object[]}  intervals          Time Intervals to edit
     * @apiParam {Integer}   intervals.id       Time Interval ID
     * @apiParam {Integer}   intervals.task_id  Task ID
     *
     * @apiParamExample {json} Request Example
     * {
     *   "intervals": [
     *     {
     *       "id": 12,
     *       "task_id": 12
     *     },
     *     {
     *       "id": 13,
     *       "task_id": 16
     *     }
     *   ]
     * }
     *
     * @apiSuccess {String}     message    Message from server
     * @apiSuccess {Integer[]}  updated    Updated intervals
     * @apiSuccess {Integer[]}  not_found  Not found intervals
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "message": "Intervals successfully updated",
     *    "updated": [12, 123, 45],
     *  }
     *
     * @apiSuccessExample {json} Not all intervals updated Response Example
     *  HTTP/1.1 207 Multi-Status
     *  {
     *    "message": "Some intervals have not been updated",
     *    "updated": [12, 123, 45],
     *    "not_found": [154, 77, 66]
     *  }
     *
     * @apiUse          400Error
     * @apiUse          ValidationError
     * @apiUse          UnauthorizedError
     * @apiUse          ForbiddenError
     */

    /**
     * @throws Throwable
     * @api             {post} /time-intervals/remove Destroy
     * @apiDescription  Destroy Time Interval
     *
     * @apiVersion      1.0.0
     * @apiName         Destroy
     * @apiGroup        Time Interval
     *
     * @apiUse          AuthHeader
     *
     * @apiPermission   time_intervals_remove
     * @apiPermission   time_intervals_full_access
     *
     * @apiParam {Integer}  id  ID of the target interval
     *
     * @apiParamExample {json} Request Example
     * {
     *   "id": 1
     * }
     *
     * @apiSuccess {String}   message  Destroy status
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "message": "Item has been removed"
     *  }
     *
     * @apiUse          400Error
     * @apiUse          ValidationError
     * @apiUse          ForbiddenError
     * @apiUse          UnauthorizedError
     */
    public function destroy(DestroyTimeIntervalRequest $request): JsonResponse
    {
        return $this->_destroy($request);
    }

    /**
     * @param BulkEditTimeIntervalRequest $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function bulkEdit(BulkEditTimeIntervalRequest $request): JsonResponse
    {
        $intervalsData = collect(
            Filter::process(Filter::getRequestFilterName(), $request->validated())['intervals']
        );

        $this->getQuery(['id' => ['in', $intervalsData->pluck('id')->toArray()]])
            ->each(static fn(Model $item) => Filter::process(
                Filter::getActionFilterName(),
                $item->fill(
                    Arr::only(
                        $intervalsData->where('id', $item->id)->first() ?: [],
                        'task_id'
                    )
                )
            )->save());

        return responder()->success()->respond(204);
    }

    /**
     * @throws Exception
     * @api             {post} /time-intervals/bulk-remove Bulk Destroy
     * @apiDescription  Multiple Destroy TimeInterval
     *
     * @apiVersion      1.0.0
     * @apiName         Bulk Destroy
     * @apiGroup        Time Interval
     *
     * @apiUse          AuthHeader
     *
     * @apiPermission   time_intervals_bulk_remove
     * @apiPermission   time_intervals_full_access
     *
     * @apiParam {Integer[]}  intervals  Intervals ID to delete
     *
     * @apiParamExample {json} Request Example
     * {
     *   "intervals": [ 1, 2, 3 ]
     * }
     *
     * @apiSuccess {String}     message    Message from server
     * @apiSuccess {Integer[]}  removed    Removed intervals
     * @apiSuccess {Integer[]}  not_found  Not found intervals
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "message": "Intervals successfully removed",
     *    "removed": [12, 123, 45],
     *  }
     *
     * @apiSuccessExample {json} Not all intervals removed Response Example
     *  HTTP/1.1 207 Multi-Status
     *  {
     *    "message": "Some intervals have not been removed",
     *    "removed": [12, 123, 45],
     *    "not_found": [154, 77, 66]
     *  }
     *
     * @apiUse          400Error
     * @apiUse          ValidationError
     * @apiUse          ForbiddenError
     * @apiUse          UnauthorizedError
     *
     */
    public function bulkDestroy(BulkDestroyTimeIntervalRequest $request): JsonResponse
    {
        $intervalIds = Filter::process(Filter::getRequestFilterName(), $request->validated())['intervals'];

        $itemsQuery = $this->getQuery(['id' => ['in', $intervalIds]]);

        Event::dispatch(Filter::getBeforeActionEventName(), $intervalIds);

        $itemsQuery->eachById(static fn($item) => Filter::process(Filter::getActionFilterName(), $item)->delete());

        Event::dispatch(Filter::getAfterActionEventName(), $intervalIds);

        return responder()->success()->respond(204);
    }

    public function trackApp(TrackAppRequest $request): JsonResponse
    {
        $user = auth()->user();
        if (!isset($user)) {
            abort(401);
        }

        $item = TrackedApplication::create(array_merge($request->validated(), ['user_id' => $user->id]));

        return responder()->success($item)->respond();
    }

    /**
     * @throws Throwable
     */
    public function create(CreateTimeIntervalRequest $request): JsonResponse
    {
        Filter::listen(
            Filter::getRequestFilterName(),
            static function (array $requestData) {
                $timezone = Settings::scope('core')->get('timezone', 'UTC');

                $requestData['start_at'] = Carbon::parse($requestData['start_at'])->setTimezone($timezone);
                $requestData['end_at'] = Carbon::parse($requestData['end_at'])->setTimezone($timezone);

                return $requestData;
            }
        );

        $screenshotService = $this->screenshotService;

        Event::listen(
            Filter::getAfterActionEventName(),
            static function (array $data) use ($request, $screenshotService) {
                if ($request->hasFile('screenshot') && optional($request->file('screenshot'))->isValid()) {
                    $screenshotService->saveScreenshot($request->file('screenshot'), $data[0]);
                }

                if (User::find($data[1]['user_id'])->web_and_app_monitoring) {
                    AssignAppsToTimeInterval::dispatch($data[0]);
                }
            }
        );

        return $this->_create($request);
    }

    public function showScreenshot(ScreenshotRequest $request, TimeInterval $interval): BinaryFileResponse
    {
        $path = $this->screenshotService->getScreenshotPath($interval);
        if (!Storage::exists($path)) {
            abort(404);
        }

        $fullPath = Storage::path($path);

        return response()->file($fullPath);
    }

    public function showThumbnail(ScreenshotRequest $request, TimeInterval $interval): BinaryFileResponse
    {
        $path = $this->screenshotService->getThumbPath($interval);
        if (!Storage::exists($path)) {
            abort(404);
        }

        $fullPath = Storage::path($path);

        return response()->file($fullPath);
    }

    public function putScreenshot(PutScreenshotRequest $request, TimeInterval $interval): JsonResponse
    {
        $data = $request->validated();

        abort_if(
            Storage::exists($this->screenshotService->getScreenshotPath($interval)),
            409,
            __('Screenshot for requested interval already exists')
        );

        $this->screenshotService->saveScreenshot($data['screenshot'], $interval);

        return responder()->success()->respond(204);
    }
}
