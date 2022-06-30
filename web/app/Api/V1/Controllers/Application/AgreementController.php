<?php

namespace App\Api\V1\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Class AgreementController
 *
 * @package App\Api\V1\Controllers
 */
class AgreementController extends Controller
{
    /**
     * Saving the user's acceptance of the agreement
     *
     * @OA\Patch(
     *     path="/users/agreement",
     *     summary="Saving the user's acceptance of the agreement",
     *     description="Saving the user's acceptance of the agreement",
     *     tags={"Users | Agreement"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="is_agreement",
     *                 type="boolean",
     *                 description="Accept User agreement",
     *                 example="true"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Successfully save"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation failed"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Unknown error"
     *     )
     * )
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function __invoke(Request $request): mixed
    {
        // Try to save received data
        try {
            // Validate input
            $this->validate($request, [
                'is_agreement'
            ]);

            // Find exist user
            $user = User::findOrFail(Auth::user()->id);
            $user->fill($request->all());
            // $user->status = User::STATUS_STEP_4;
            $user->save();

            // Return response to client
            return response()->jsonApi([
                'type' => 'success',
                'title' => 'User agreement',
                'message' => "User agreement set successfully",
                'data' => []
            ], 200);
        } catch (ValidationException $e) {
            return response()->jsonApi([
                'type' => 'warning',
                'title' => 'User agreement',
                'message' => "Validation error: " . $e->getMessage(),
                'data' => null
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'User agreement',
                'message' => "User not found: {$e->getMessage()}",
                'data' => null
            ], 404);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'User agreement',
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }
}