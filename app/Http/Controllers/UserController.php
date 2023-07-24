<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    
    public function index()
    {
        $o_all = User::where('status', true)->get();
        return response()->json([
            'data' => $o_all
        ], 200);
    }

    public function add(UserRequest $request)
    {
        $validatedData = Validator::make(request()->post(), $request->rules(), $request->messages());
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
        $o = User::create(request()->post()); // Tiene Observable en el modelo
        return response()->json([
            'message' => 'Se ha agregado el usuario correctamente',
            'data' => $o
        ], 201);
    }

    public function edit($id = '')
    {
        $o = User::where('status', true)->where('id', $id)->firstOrFail();

        return response()->json([
            'message' => 'Se ha actualizado el usuario correctamente',
            'data' => $o
        ], 200);
    }

}
