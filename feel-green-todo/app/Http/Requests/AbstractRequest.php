<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Abstract Request
 *
 * @copyright Elena Cretul
 */
abstract class AbstractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Always return true, because we don't use Laravel Authorization.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
