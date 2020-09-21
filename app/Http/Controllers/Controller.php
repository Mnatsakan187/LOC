<?php



namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



/**
 * @OA\OpenApi(
 *     @OA\Server(
 *         url="/api/v1",
 *         description="LOC API server"
 *     ),
 *     @OA\Info(
 *         version="1.0.0",
 *         title="LOC APIs",
 *         description="API documentation for LOC",
 *         @OA\Contact(name="Josh - josh@obiwansoft.com")
 *     ),
 * )
 */
/**
 * @OA\Schema(
 *     schema="ErrorModel",
 *     required={"code", "message"},
 *     @OA\Property(
 *         property="code",
 *         type="integer",
 *         format="int32"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string"
 *     )
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
