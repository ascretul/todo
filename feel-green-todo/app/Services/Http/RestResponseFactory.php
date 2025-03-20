<?php

declare(strict_types=1);

namespace App\Services\Http;

use App\Exceptions\HttpException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;
use JsonSerializable;
use Throwable;

/**
 * Rest Response Factory
 *
 * @copyright Elena Cretul
 */
class RestResponseFactory extends ResponseFactory
{
    /**
     * Constructor.
     *
     * @param Repository $configRepository
     * @param Factory $viewFactory
     * @param Redirector $redirector
     */
    public function __construct(private readonly Repository $configRepository, Factory $viewFactory, Redirector $redirector)
    {
        parent::__construct($viewFactory, $redirector);
    }

    /**
     * Successful server response
     *
     * @param JsonSerializable|array|null $data
     * @param int $code
     * @param array $headers
     *
     * @return JsonResponse
     */
    public function ok(array|JsonSerializable|null $data = null, int $code = JsonResponse::HTTP_OK, array $headers = []): JsonResponse
    {
        return $this->json($data, $code, $headers);
    }

    /**
     * Json No Content response.
     *
     * @param int $code
     * @param array $headers
     *
     * @return JsonResponse
     */
    public function jsonNoContent(int $code = JsonResponse::HTTP_NO_CONTENT, array $headers = []): JsonResponse
    {
        return $this->json(null, $code, $headers);
    }

    /**
     * Bad client request
     *
     * @param string $message
     * @param array|null $errors
     *
     * @return JsonResponse
     */
    public function bad(string $message = '', ?array $errors = null): JsonResponse
    {
        $message = $message ?: (string)$this->configRepository->get('api.responses.400.message');

        return $this->json(['message' => $message, 'errors' => $errors ?: null], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Resource not found
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function missing(string $message = ''): JsonResponse
    {
        $message = ($this->configRepository->get('app.debug', false) && $message)
            ? $message
            : (string)$this->configRepository->get('api.responses.404.message');

        return $this->json(['message' => $message], JsonResponse::HTTP_NOT_FOUND, ['X-Robots-Tag' => 'noindex']);
    }

    /**
     * Permission not allowed
     *
     * @param string $message
     * @param int|null $errorCode
     *
     * @return JsonResponse
     */
    public function forbidden(string $message = '', ?int $errorCode = null): JsonResponse
    {
        $message = $message ?: (string)$this->configRepository->get('api.responses.403.message');
        $data = $errorCode
            ? ['message' => $message, 'code' => $errorCode]
            : ['message' => $message];

        return $this->json($data, JsonResponse::HTTP_FORBIDDEN);
    }

    /**
     * Unauthorized client
     *
     * @param string $message
     * @param int|null $errorCode
     *
     * @return JsonResponse
     */
    public function unauthorized(string $message = '', ?int $errorCode = null): JsonResponse
    {
        $message = $message ?: (string)$this->configRepository->get('api.responses.401.message');
        $data = $errorCode
            ? ['message' => $message, 'code' => $errorCode]
            : ['message' => $message];

        return $this->json($data, JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Unprocessable entity - the data is correct but a logic exception happened
     *
     * @param array|string|null $data
     *
     * @return JsonResponse
     */
    public function unprocessable(array|string|null $data = null): JsonResponse
    {
        $data = \is_string($data) ? ['message' => $data] : $data;

        return $this->json($data, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Too Many Requests - should be used to limit the amount of requests clients make.
     * It's possible to tell client when to try another request with the Retry-After header, but it's optional.
     *
     * @param string $message
     * @param int|null $errorCode
     * @param int $retryAfterMinutes
     *
     * @return JsonResponse
     */
    public function tooManyRequests(
        string $message = '',
        int    $retryAfterMinutes = 0,
        ?int   $errorCode = null,
    ): JsonResponse
    {
        $retryAfterInterval = $retryAfterMinutes * 60;
        $headers = $retryAfterInterval ? ['Retry-After' => $retryAfterInterval] : [];
        $data = $errorCode ? ['message' => $message, 'code' => $errorCode] : ['message' => $message];

        return $this->json($data, JsonResponse::HTTP_TOO_MANY_REQUESTS, $headers);
    }

    /**
     * Internal server error
     *
     * @param Throwable $exception
     * @param string $message
     *
     * @return JsonResponse
     */
    public function serverError(Throwable $exception, string $message = ''): JsonResponse
    {
        $isNotProductionEnvironment = !((string)$this->configRepository->get('app.environment') === 'production');
        $hasDebugModeEnabled = (bool)$this->configRepository->get('app.debug', false);

        if ($isNotProductionEnvironment && $hasDebugModeEnabled) {
            $responsePayload = [
                'message' => $message ? $message . ' ' . $exception->getMessage() : $exception->getMessage(),
                'file' => $exception->getFile(),
                'trace' => $exception->getTrace(),
            ];
        } else {
            $responsePayload = [
                'message' => $message ?: (string)$this->configRepository->get('api.responses.500.message')
            ];
        }

        return $this->json(
            $responsePayload,
            JsonResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Custom server response
     *
     * @param array|JsonSerializable|null $data
     * @param int $code
     *
     * @return JsonResponse
     */
    public function response(array|JsonSerializable|null $data, int $code): JsonResponse
    {
        return $this->json($data, $code);
    }

    /**
     * From HTTP Exception
     *
     * @param HttpException $exception
     *
     * @return JsonResponse
     */
    public function fromHttpException(HttpException $exception): JsonResponse
    {
        $message = $exception->getMessage() ?: (string)$this->configRepository->get('api.responses.default.message');

        return $this->json(['message' => $message], $exception->getCode());
    }
}
