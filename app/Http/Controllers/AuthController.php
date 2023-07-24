<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Info(
 *     title="API Test",
 *     version="1.0.0",
 *     description="Esta es una API de prueba para mostrar a las empresas y poder ser contratado. Espero que le guste :)",
 *     @OA\Contact(
 *          email="lanlion000128@gmail.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 */

 // https://stackoverflow.com/questions/50614594/use-jwt-bearer-token-in-swagger-laravel

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
    * @OA\Post(
    *      path="/api/auth/login",
    *      operationId="Iniciar Sesión",
    *      tags={"Authentication"},
    *      summary="Inicia la sesión",
    *      description="Inicia la sesión del usuario",
    *      @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 schema="Request",
    *                 title="Title",
    *                 required={"email", "password"},
    *                 @OA\Property(
    *                     title="Correo",
    *                     description="El correo del usuario a loguear",
    *                     property="email",
    *                     type="string",
    *                     example="ddf@gmail.com"
    *                 ),
    *                 @OA\Property(
    *                     title="Contraseña",
    *                     description="La contraseña del usuario a loguear",
    *                     property="password",
    *                     type="string",
    *                     example="123456"
    *                 ),
    *                 example={"email": "ddf@gmail.com", "password": "123456"}
    *             )
    *         )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Success",
    *          @OA\JsonContent(
    *               example={
    *                   "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ",
    *                   "token_type": "bearer",
    *                   "expires_in": 3600
    *               }
    *          )
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unahutorized",
    *          @OA\JsonContent(
    *               example={
    *                   "message": "Unahutorized"
    *               }
    *          )
    *      ),
    * )
    */
    public function login()
    {
        $token = '';
        if (
            !User::where('email', request()->post('email'))
            ->where('status', true)
            ->exists() && !Hash::check(request()->post('password'))
        ) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } else {
            $user = User::where('email', request()->post('email'))->first();
            $token = auth()->login($user);
        }

        return $this->respondWithToken($token);
    }

    /**
    * @OA\Get(
    *      path="/api/auth/me",
    *      operationId="Mostrar Datos del Usaurio",
    *      tags={"Authentication"},
    *      summary="Mostrar Datos del Usaurio",
    *      description="Mostrar Datos del Usaurio logueado",
    *      security={{"bearerAuth":{}}},
    *      @OA\Property(
    *           property="allowedMethods",
    *           type="string",
    *           description="Métodos permitidos para esta ruta",
    *           enum={"GET", "POST"}
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Success",
    *          @OA\JsonContent(
    *               example={
    *                   "id": 1,
    *                   "name": "Domingo Díaz Feriado",
    *                   "email": "ddf@gmail.com",
    *                   "photo": "null",
    *                   "email_verified_at": null,
    *                   "role": 1,
    *                   "status": true,
    *                   "created_at": "2023-07-23T19:16:06.000000Z",
    *                   "updated_at": "2023-07-23T19:16:06.000000Z"
    *               }
    *          )
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unahutorized",
    *          @OA\JsonContent(
    *               example={
    *                   "message": "Unahutorized"
    *               }
    *          )
    *      ),
    * )
    */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
    * @OA\Get(
    *      path="/api/auth/logout",
    *      operationId="Cerrar Sesión",
    *      tags={"Authentication"},
    *      summary="Cierra la sesión",
    *      description="Cerrar la sesión del usuario logueado",
    *      security={{"bearerAuth":{}}},
    *      @OA\Property(
    *           property="allowedMethods",
    *           type="string",
    *           description="Métodos permitidos para esta ruta",
    *           enum={"GET", "POST"}
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Success",
    *          @OA\JsonContent(
    *               example={
    *                   "message": "Ha cerrado sesión correctamente"
    *               }
    *          )
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unahutorized",
    *          @OA\JsonContent(
    *               example={
    *                   "message": "Unahutorized"
    *               }
    *          )
    *      ),
    * )
    */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Ha cerrado sesión correctamente']);
    }

    /**
    * @OA\Get(
    *      path="/api/auth/refresh",
    *      operationId="Refrescar Token",
    *      tags={"Authentication"},
    *      summary="Refresca el access_token",
    *      description="Refresca el access_token del usuario",
    *      security={{"bearerAuth":{}}},
    *      @OA\Response(
    *          response=200,
    *          description="Success",
    *          @OA\JsonContent(
    *               example={
    *                   "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ",
    *                   "token_type": "bearer",
    *                   "expires_in": 3600
    *               }
    *          )
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unahutorized",
    *          @OA\JsonContent(
    *               example={
    *                   "message": "Unahutorized"
    *               }
    *          )
    *      ),
    * )
    */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
    * @OA\Post(
    *    path="/api/auth/register",
    *    summary="Registrar un usuario",
    *    tags={"Authentication"},
    *    @OA\RequestBody(
    *       required=true,
    *       @OA\MediaType(
    *           mediaType="multipart/form-data",
    *           @OA\Schema(
    *               schema="Request",
    *               title="Title",
    *               required={"name", "email", "password", "role"},
    *               @OA\Property(
    *                   title="Nombre",
    *                   description="Nombre del Usuario",
    *                   property="name",
    *                   type="string",
    *                   example="Domingo Díaz Feriado"
    *               ),
    *               @OA\Property(
    *                   title="Correo",
    *                   description="Correo del Usuario",
    *                   property="email",
    *                   type="string",
    *                   example="ddf@gmail.com"
    *               ),
    *               @OA\Property(
    *                   title="Imagen",
    *                   description="Imagen del Perfil",
    *                   property="photo",
    *                   type="file",
    *                   nullable=true,
    *                   format="binary"
    *               ),
    *               @OA\Property(
    *                   title="Contraseña",
    *                   description="Contraseña del Usuario",
    *                   property="password",
    *                   type="string",
    *                   example="123456",
    *                   pattern="\w{6,}"
    *               ),
    *               @OA\Property(
    *                   title="Rol",
    *                   description="Rol del Usuario. 1 -> Escritor y 2 -> Lector",
    *                   property="role",
    *                   type="integer",
    *                   example="1",
    *                   format="int32",
    *                   pattern="^(1|2)$"
    *               ),
    *               @OA\Property(
    *                   title="Apodo",
    *                   description="Apodo del Escritor.",
    *                   property="nickname",
    *                   type="string",
    *                   example="LANGEL"
    *               ),
    *               @OA\Property(
    *                   title="Fecha de Nacimiento",
    *                   description="Fecha de Nacimiento del Escritor.",
    *                   property="birth_date",
    *                   type="string",
    *                   format="date",
    *                   example="1990-01-01"
    *               ),
    *               @OA\Property(
    *                   title="Género Principal",
    *                   description="Género Principal del Escritor.",
    *                   property="principal_gender",
    *                   type="string",
    *                   example="Emprendimiento"
    *               ),
    *               @OA\Property(
    *                   title="Estado",
    *                   description="Estado del Escritor.",
    *                   property="status",
    *                   type="string",
    *                   example="Soltero"
    *               ),
    *               @OA\Property(
    *                   title="Descripción",
    *                   description="Descripción del Escritor.",
    *                   property="description",
    *                   type="string",
    *                   nullable=true
    *               ),
    *               @OA\Property(
    *                   title="Género Favorito",
    *                   description="Género Favorito del Lector.",
    *                   property="favorite_gender",
    *                   type="string",
    *                   example="Terror"
    *               ),
    *               @OA\Property(
    *                   title="Horas de Lecturas",
    *                   description="Horas de Lecturas del Lector.",
    *                   property="reading_hours",
    *                   type="integer",
    *                   example=2
    *               ),
    *               example={
    *                   "name": "Domingo Díaz Feriado",
    *                   "email": "ddf@gmail.com",
    *                   "password": "123456",
    *                   "photo": null,
    *                   "role": 1
    *               }
    *           )
    *       ),
    *       @OA\JsonContent(
    *           example={
    *               "name": "Domingo Díaz Feriado",
    *               "email": "ddl@gmail.com",
    *               "photo": null,
    *               "password": "123456",
    *               "role": 1
    *           }
    *       )
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="Success",
    *       @OA\JsonContent(
    *           example={
    *               "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ",
    *               "token_type": "bearer",
    *               "expires_in": 3600
    *           }
    *       )
    *   ),
    *   @OA\Response(
    *       response=400,
    *       description="Bad Request",
    *       @OA\JsonContent(
    *           example={
    *               "description": "Ha ocurrido un error al validar los datos",
    *               "error": "El correo es requerido"
    *           }
    *       )
    *   ),
    * )
    */
    public function register(RegisterRequest $request)
    {
        $validatedData = Validator::make($request->all(), $request->rules(), $request->messages());
        // Quiero que la imagen sea png o jpg pero si no la envia me salta el error, estuve investigando
        // pero nada concreto. La idea sería que salte el error solo cuando se envía. Si alguien tiene alguna solución :)
        if(request()->hasFile('file') && request()->file('file')->isValid()) {
            $validatedFile = Validator::make(request()->post(), [
                'photo' => 'file|mimes:jpeg,png,jpg',
            ], [
                'photo.file' => 'La imagen no es un archivo',
                'photo.mimes' => 'La imagen debe ser png o jpg',
            ]);
            if($validatedFile->fails()) {
                return response()->json([
                    'description' => 'Ha ocurrido un error al validar los datos',
                    'error' => $validatedFile->errors()->first(),
                ], 400);
            }
        }
        if($validatedData->fails()) {
            return response()->json([
                'description' => 'Ha ocurrido un error al validar los datos',
                'error' => $validatedData->errors()->first(),
            ], 400);
        }
        $user = User::create(request()->post()); // Tiene Observable en el modelo
        $token = auth()->login($user);
        Log::debug(auth()->user());
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
