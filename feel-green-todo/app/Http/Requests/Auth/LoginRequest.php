<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\AbstractApiRequest;
use App\Services\Http\RestResponseFactory;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class LoginRequest extends AbstractApiRequest
{
    public function __construct(private readonly RestResponseFactory $restResponseFactory)
    {
        parent::__construct();
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    protected function response(array $errors): HttpResponse
    {
        return $this->restResponseFactory->bad('Login Request cannot be validated.', $errors);
    }
}
