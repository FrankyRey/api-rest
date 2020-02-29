<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prueba/{nombre?}', function($nombre = null) {
    $texto = '<h2>Texto desde una ruta</h2>';
    $texto .= 'Nombre: '. $nombre;
    return view('pruebas', array(
        'texto' => $texto
    ));
});

Route::get('/pruebas/animales', 'PruebasController@index');

Route::get('/pruebas/orm', 'PruebasController@testORM');

Route::get('/usuario/pruebas', 'UserController@pruebas');
Route::get('/orden/pruebas', 'OrdenController@pruebas');
Route::get('/estatusOrden/pruebas', 'EstatusOrdenController@pruebas');

/* Métodos HTTP

 * GET: Conseguir datos o recursos
 * POST: Guardar datos o recursos, o hacer lógica desde un formulario.
 * PUT: Actualizar datos o recursos.
 * DELETE: Eliminar datos o recursos.

*/
Route::post('/api/user/register', 'UserController@register');
Route::post('api/user/login', 'UserController@login');
Route::put('api/user/update', 'UserController@update');
Route::post('api/user/upload', 'UserController@upload');