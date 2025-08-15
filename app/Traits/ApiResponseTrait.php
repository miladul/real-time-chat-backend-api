<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function successResponse($data = null, string $message = 'Success', int $status = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    protected function errorResponse(string $message = 'Error', int $status = 400, $errors = null)
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    protected function validationErrorResponse($errors, string $message = 'Validation errors', int $status = 422)
    {
        return $this->errorResponse($message, $status, $errors);
    }
}
