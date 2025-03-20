<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\AbstractApiRequest;
use App\Services\Http\RestResponseFactory;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Login Api Request
 *
 * @copyright Elena Cretul
 */
class LoginApiRequest extends AbstractApiRequest
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
