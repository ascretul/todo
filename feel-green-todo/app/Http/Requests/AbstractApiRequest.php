<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstract Api Request
 *
 * @copyright Elena Cretul
 */
abstract class AbstractApiRequest extends AbstractRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     *
     * @throws LaravelValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw (new LaravelValidationException($validator, $this->response($validator->errors()->messages())))
            ->errorBag($this->errorBag);
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array $errors
     *
     * @return Response
     */
    abstract protected function response(array $errors): Response;
}
