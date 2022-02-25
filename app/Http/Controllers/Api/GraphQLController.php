<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Entities\GraphQLBatchingException;
use App\Http\Controllers\Controller;
use GraphQL\Server\OperationParams as BaseOperationParams;
use GraphQL\Server\RequestError;
use Illuminate\Http\JsonResponse;
use Laragraph\Utils\RequestParser;
use Rebing\GraphQL\GraphQL;
use Rebing\GraphQL\Helpers;
use Rebing\GraphQL\Support\OperationParams;
use Throwable;

class GraphQLController extends Controller
{
    public function __construct(private RequestParser $parser, private GraphQL $graphql)
    {
    }

    /**
     * @throws RequestError
     * @throws Throwable
     */
    public function __invoke(string $schema): JsonResponse
    {
        $operations = $this->parser->parseRequest(request());

        throw_if(
            is_array($operations) && !config('graphql.batching.enable', true),
            new GraphQLBatchingException()
        );

        $graphql = $this->graphql;

        $result = Helpers::applyEach(
            static fn(BaseOperationParams $baseOperationParams
            ) => $graphql->execute($schema, new OperationParams($baseOperationParams)),
            $operations
        );

        return responder()->success($result['data'])->meta(['errors' => $result['errors'] ?? null])->respond(
            isset($result['errors']) ? 207 : 200
        );
    }
}
