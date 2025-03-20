<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\MissingAuthenticationException;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginApiRequest;
use App\Services\Auth\TokenAuthenticator;
use App\Services\Http\RestResponseFactory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

/**
 * Authentication API controller
 *
 * @copyright Elena Cretul
 *
 * @todo Rename methods
 */
class AuthenticationApiController extends Controller
{
    /**
     * Constructor
     *
     * @param TokenAuthenticator $tokenAuthenticator
     * @param RestResponseFactory $restResponseFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly TokenAuthenticator $tokenAuthenticator,
        private readonly RestResponseFactory $restResponseFactory,
        private readonly LoggerInterface $logger,
    ){
    }

    /**
     * Handle an incoming authentication request
     *
     * @param LoginApiRequest $request
     *
     * @return JsonResponse
     */
    public function store(LoginApiRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        try {
            $token = $this->tokenAuthenticator->authenticate($credentials, $request);

            return $this->restResponseFactory->ok([
                'token' => $token,
                'user' => $request->user()
            ]);
        } catch (ValidationException $exception) {
            return $this->restResponseFactory->bad($exception->getMessage());
        } catch (MissingAuthenticationException $exception) {
            $this->logger->warning('Failed login attempt', ['email' => $credentials['email'], 'ip' => $request->ip()]);

            return $this->restResponseFactory->unauthorized($exception->getMessage());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return $this->restResponseFactory->serverError($exception);
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->restResponseFactory->jsonNoContent();
    }
}
