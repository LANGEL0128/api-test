<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
    * @OA\Get(
    *      path="/api/user",
    *      operationId="Mostrar Usuarios",
    *      tags={"Users"},
    *      summary="Mostrar Usuarios",
    *      security={{"bearerAuth":{}}},
    *      description="Muestra todos los usuarios",
    *      @OA\Response(
    *          response=200,
    *          description="Success",
    *           @OA\JsonContent(
    *              example={
    *                "message": "Se ha cargado los usuarios correctamente",
    *                "data": {
    *                  {
    *                    "id": 1,
    *                    "name": "Admin",
    *                    "email": "admin@admin.com",
    *                    "photo": null,
    *                    "email_verified_at": null,
    *                    "role": 3,
    *                    "status": true,
    *                    "created_at": "2023-07-24T20:29:45.000000Z",
    *                    "updated_at": "2023-07-24T20:29:45.000000Z"
    *                  },
    *                  {
    *                    "id": 2,
    *                    "name": "Domingo Díaz Feriado",
    *                    "email": "ddf@gmail.com",
    *                    "photo": null,
    *                    "email_verified_at": null,
    *                    "role": 1,
    *                    "status": true,
    *                    "created_at": "2023-07-24T20:31:02.000000Z",
    *                    "updated_at": "2023-07-24T20:31:02.000000Z",
    *                    "writerprofile": {
    *                      "id": 1,
    *                      "user_id": 2,
    *                      "nickname": "LANGEL",
    *                      "principal_gender": "Emprendimiento",
    *                      "description": null,
    *                      "birth_date": "1990-01-01",
    *                      "status": "Soltero",
    *                      "created_at": "2023-07-24T20:31:02.000000Z",
    *                      "updated_at": "2023-07-24T20:31:02.000000Z"
    *                    }
    *                  }
    *                }
    *              }
    *          )
    *       ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthenticated",
    *           @OA\JsonContent(
    *              example={
    *                   "message": "Unauthenticated"
    *              }
    *          )
    *       ),
    *      @OA\Response(
    *          response=403,
    *          description="Not Authorized",
    *           @OA\JsonContent(
    *              example={
    *                   "message": "Not Authorized"
    *              }
    *          )
    *       ),
    *     )
    */
    public function index()
    {
        $o_all = User::where('status', true)->get()->map(function($user) {
            if($user->role == 1) {
                $user->load('writerprofile');
            } elseif ($user->role == 2) {
                $user->load('readerprofile');
            }
            return $user;
        });
        return response()->json([
            'message' => 'Se ha cargado los usuarios correctamente',
            'data' => $o_all
        ], 200);
    }

    /**
    * @OA\Post(
    *    path="/api/user",
    *    summary="Agregar un usuario",
    *    tags={"Users"},
    *    security={{"bearerAuth":{}}},
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
    *       response=201,
    *       description="Success",
    *       @OA\JsonContent(
    *           example={
    *               "message": "Se ha creado el usuario correctamente",
    *               "data": {
    *                   "message": "Se ha agregado el usuario correctamente",
    *                   "data": {
    *                       "name": "Pedro Diaz",
    *                       "role": "2",
    *                       "email": "pedro@gmail.com",
    *                       "photo": "/storage/users/4hUQC6q4Lff6pfO68ziLx9yaPfYL8RvrYp0FnHPB.png",
    *                       "updated_at": "2023-07-25T01:04:29.000000Z",
    *                       "created_at": "2023-07-25T01:04:29.000000Z",
    *                       "id": 3
    *                   }
    *               }
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
    *   @OA\Response(
    *       response=401,
    *       description="Unahutorized",
    *       @OA\JsonContent(
    *           example={
    *               "message": "Unahutorized"
    *           }
    *       )
    *   ),
    *   @OA\Response(
    *       response=403,
    *       description="Not Authorized",
    *       @OA\JsonContent(
    *           example={
    *               "message": "Not Authorized"
    *           }
    *       )
    *   ),
    * )
    */
    public function add(UserRequest $request)
    {
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
        $o = User::create(request()->post()); // Tiene Observable en el modelo
        return response()->json([
            'message' => 'Se ha agregado el usuario correctamente',
            'data' => $o
        ], 201);
    }

    /**
    * @OA\Post(
    *    path="/api/user/{id}",
    *    summary="Editar un usuario",
    *    tags={"Users"},
    *    security={{"bearerAuth":{}}},
    *    @OA\Parameter(
    *        description="ID del usuario a actualizar",
    *        in="path",
    *        name="id",
    *        required=true,
    *        example=1
    *    ),
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
    *               "message": "Se ha actualizado el usuario correctamente",
    *               "data": {
    *                   "message": "Se ha actualizado el usuario correctamente",
    *                   "data": {
    *                       "name": "Pedro Diaz",
    *                       "role": "2",
    *                       "email": "pedro@gmail.com",
    *                       "photo": "/storage/users/4hUQC6q4Lff6pfO68ziLx9yaPfYL8RvrYp0FnHPB.png",
    *                       "updated_at": "2023-07-25T01:04:29.000000Z",
    *                       "created_at": "2023-07-25T01:04:29.000000Z",
    *                       "id": 3
    *                   }
    *               }
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
    *   @OA\Response(
    *       response=401,
    *       description="Unahutorized",
    *       @OA\JsonContent(
    *           example={
    *               "message": "Unahutorized"
    *           }
    *       )
    *   ),
    *   @OA\Response(
    *       response=403,
    *       description="Not Authorized",
    *       @OA\JsonContent(
    *           example={
    *               "message": "Not Authorized"
    *           }
    *       )
    *   ),
    *   @OA\Response(
    *       response=404,
    *       description="Not Found",
    *       @OA\JsonContent(
    *           example={
    *               "message": "Not Found"
    *           }
    *       )
    *   ),
    * )
    */
    public function edit(UserRequest $request, $id = '')
    {
        if(!$o = User::where('status', true)->where('id', $id)->first()) {
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
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
        $o->update(request()->post());
        return response()->json([
            'message' => 'Se ha actualizado el usuario correctamente',
            'data' => $o
        ], 200);
    }

    /**
    * @OA\Get(
    *      path="/api/user/{id}",
    *      operationId="Mostrar Datos de un Usuario",
    *      tags={"Users"},
    *      summary="Mostrar Datos de un Usuario",
    *      description="Mostrar Datos de un Usuario",
    *      security={{"bearerAuth":{}}},
    *      @OA\Parameter(
    *          description="ID del usuario a mostrar",
    *          in="path",
    *          name="id",
    *          required=true,
    *          example="1"
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Success",
    *          @OA\JsonContent(
    *               example={
    *                 "data": {
    *                   "id": 4,
    *                   "name": "Luis Angel",
    *                   "email": "luis@gmail.com",
    *                   "photo": "/storage/users/LyqUyd4u2eP9NuWgzxNCuFmAO8U5rF7ftAA5W1sm.png",
    *                   "email_verified_at": null,
    *                   "role": 1,
    *                   "status": true,
    *                   "created_at": "2023-07-25T01:36:06.000000Z",
    *                   "updated_at": "2023-07-25T01:36:49.000000Z",
    *                   "writerprofile": {
    *                     "id": 2,
    *                     "user_id": 4,
    *                     "nickname": "LuisAng",
    *                     "principal_gender": "Emprendimiento",
    *                     "description": null,
    *                     "birth_date": "1990-01-01",
    *                     "status": "Soltero",
    *                     "created_at": "2023-07-25T01:36:06.000000Z",
    *                     "updated_at": "2023-07-25T01:36:06.000000Z"
    *                   }
    *                 }
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
    *      @OA\Response(
    *          response=403,
    *          description="Not Authorized",
    *          @OA\JsonContent(
    *               example={
    *                   "message": "Not Authorized"
    *               }
    *          )
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not Found",
    *          @OA\JsonContent(
    *               example={
    *                   "message": "Not Found"
    *               }
    *          )
    *      ),
    * )
    */
    public function show($id = '')
    {
        if(!$o = User::where('status', true)->where('id', $id)->first()) {
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        if($o->role == 1) {
            $o->writerprofile;
        } elseif ($o->role == 2) {
            $o->readerprofile;
        }
        return response()->json([
            'data' => $o
        ], 200);
    }

    /**
    * @OA\Delete(
    *      path="/api/user/delete/{id}",
    *      tags={"Users"},
    *      summary="Eliminar Usuario",
    *      description="Elimina un usuario",
    *      security={{"bearerAuth":{}}},
    *      @OA\Parameter(
    *          description="ID del usuario a eliminar",
    *          in="path",
    *          name="id",
    *          required=true,
    *          example="2"
    *      ),
    *      @OA\Response(
    *          response=204,
    *          description="Elimina el usuario satisfactoriamente",
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
    *      @OA\Response(
    *          response=403,
    *          description="Not Authorized",
    *          @OA\JsonContent(
    *               example={
    *                   "message": "Not Authorized"
    *               }
    *          )
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not Found",
    *          @OA\JsonContent(
    *               example={
    *                   "message": "Not Found"
    *               }
    *          )
    *      ),
    * )
    */
    public function delete($id = '') {
        if(!$o = User::where('id', $id)->first()) {
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        User::destroy($id);
        return response()->json([], 204);
    }

}
