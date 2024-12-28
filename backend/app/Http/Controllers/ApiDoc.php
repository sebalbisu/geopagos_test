<?php

namespace App\Http\Controllers;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="API Documentation",
 *         version="1.0.0"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="bearerAuth",
 *             type="http",
 *             scheme="bearer",
 *             bearerFormat="JWT"
 *         )
 *     )
 * )
 */
class ApiDoc
{
    // This class is only for Swagger configuration
}
