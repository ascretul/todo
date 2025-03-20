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
    public function authorize(): bool
    {
        return true;
    }
}
