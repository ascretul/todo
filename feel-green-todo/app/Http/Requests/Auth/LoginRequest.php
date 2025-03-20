<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\AbstractApiRequest;
use App\Services\Http\RestResponseFactory;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Login Request
 *
 * @copyright Elena Cretul
 *
 * @todo Rename to API Request
 */
class LoginRequest extends AbstractApiRequest
{
    /**
     * Constructor
     *
     * @param RestResponseFactory $restResponseFactory
     */
    public function __construct(private readonly RestResponseFactory $restResponseFactory)
    {
        parent::__construct();
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Response
     *
     * @param array $errors
     *
     * @return HttpResponse
     */
    protected function response(array $errors): HttpResponse
    {
        return $this->restResponseFactory->bad('Login request is not valid.', $errors);
    }
}
