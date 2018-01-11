<?php
/**
 * Created by PhpStorm.
 * User: paulb
 * Date: 05/01/2018
 * Time: 12:51
 */

namespace App\Http\Controllers;

trait JsonableTrait
{
    public $errors;

    /**
     * Handle errors in compliance with JSON API
     *
     * @param $statusCode
     * @param $title
     * @param null $detail
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($statusCode, $title, $detail = null)
    {
        return response()->json([
            'errors' => [
                'status' => $statusCode,
                'title' => $title,
                'detail' => $detail
            ]
        ], $statusCode);
    }
}