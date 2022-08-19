<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    public ?User $user;

    /**
     * Client request
     *
     * @var Request
     */
    protected $request;

    /**
     * Form validation request
     */
    protected $formRequest;

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * Response body
     *
     * @var array
     */
    protected array $body = [];

    /**
     * AbstractApiController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request  = $request;
        $this->user     = $request->user('sanctum');
    }

    /**
     * Returns response status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set response status code
     *
     * @param int $statusCode
     *
     * @return ApiController
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Append data to response body
     *
     * @param string $key
     * @param        $data
     * @param bool   $mainObject
     * @return ApiController
     */
    public function appendBody(string $key, $data, $mainObject = false): self
    {
        if ($mainObject) {
            $this->body[$key] = $data;
            return $this;
        }
        $this->body['data'][$key] = $data;

        return $this;
    }

    /**
     *
     * Append error message to response body
     *
     * @param string $message
     *
     * @return ApiController
     */
    public function appendError(string $message, int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY): self
    {
        return $this->appendBody('error', $message)
            ->setStatusCode($statusCode);
    }

    /**
     * @return JsonResponse
     */
    public function respond(): JsonResponse
    {
        $response['success'] = in_array($this->getStatusCode(), range(Response::HTTP_OK, Response::HTTP_IM_USED));

        $response = array_merge($response, $this->body);

        return \response()->json($response, $this->getStatusCode());
    }

    /**
     * Set request
     *
     * @param $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = app($request);

        return $this;
    }

    /**
     * Append data to body when condition is true
     *
     * @param $condition
     * @param ...$data
     * @return $this
     */
    public function appendBodyWhen($condition, ...$data)
    {
        if(true === $condition) {
            $this->appendBody(...$data);
        }

        return $this;
    }

    /**
     * Respond error when condition is true
     *
     * @param $condition
     * @param $message
     * @return $this
     */
    public function respondErrorWhen($condition, $message = '')
    {
        if(true === $condition) {
            throw new HttpResponseException(response()->json([
                'success'   =>  false,
                'message'   =>  $message ?: translate('Validation errors'),
                'data'      =>  []
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        return $this;
    }
}
