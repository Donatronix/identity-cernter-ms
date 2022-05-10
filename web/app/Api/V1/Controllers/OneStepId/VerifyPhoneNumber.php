<?php

namespace App\Api\V1\Controllers\OneStepId;

use App\Api\V1\Controllers\Controller;
use App\Models\TwoFactorAuth;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class VerifyPhoneNumber extends Controller
{
    /**
     * Verify Phone Number
     *
     * @OA\Post(
     *     path="/auth/send-code",
     *     summary="Verify Phone Number",
     *     description="Verify Phone Number",
     *     tags={"Auth by OneStep"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"auth_code_from_user"},
     *
     *             @OA\Property(
     *                 property="auth_code_from_user",
     *                 type="string",
     *                 description="verification code enter by user",
     *                 example="ksdaofdf"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="sid",
     *                 type="string",
     *                 example="Create new user. Step 1"
     *             ),
     *             @OA\Property(
     *                 property="validate_auth_code",
     *                 type="boolean",
     *                 example="true",
     *                 description="Indicates if validation is successful"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User was successful created"
     *             ),
     *             @OA\Property(
     *                 property="user_status",
     *                 type="number",
     *                 description="User Status INACTIVE = 0, ACTIVE = 1, BANNED = 2"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *
     *          @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 example="danger"
     *             ),
     *             @OA\Property(
     *                 property="validate_auth_code",
     *                 type="boolean",
     *                 example="false"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="User object",
     *                 example=""
     *             )
     *         )
     *     )
     * )
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        // Validate input data
        $this->validate($request, [
            'auth_code_from_user' => 'required',
        ]);

        try {
            $twoFa = TwoFactorAuth::where("code", $request->auth_code_from_user)->firstOrFail();
        } catch (ModelNotFoundException $th) {
            return response()->json([
                "type" => "danger",
                "message" => "Invalid Token",
                "validate_auth_code" => false
            ], 400);
        }

        try {
            $user = $twoFa->user;

            if ($user->status == User::STATUS_BANNED) {
                return response()->json([
                    "type" => "danger",
                    "user_status" => $user->status,
                    "sid" => $twoFa->sid,
                    "message" => "User has been banned from this platform."
                ], 403);
            }

            $user->phone_verified_at = Carbon::now();
            $user->save();
        } catch (Exception $th) {
            return response()->json([
                "message" => "Unable to verify token",
                "type" => "danger",
                "validate_auth_code" => false,
            ], 400);
        }

        return response()->json([
            "message" => "Phone Number Verification successful",
            "type" => "success",
            "sid" => $twoFa->sid,
            "user_status" => $user->status,
            "validate_auth_code" => true
        ]);
    }
}
