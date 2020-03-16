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

use App\Http\Middleware\ApiAuthMiddleware;

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

// Rutas del usuario
Route::post('/api/user/register', 'UserController@register');
Route::post('api/user/login', 'UserController@login');
Route::put('api/user/update', 'UserController@update');
Route::post('api/user/upload','UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('api/user/avatar/{filename}','UserController@getImage');
Route::get('api/user/profile/{id}','UserController@profile');
Route::get('api/user/all','UserController@index');

// Rutas de Estatus de las ordenes
Route::resource('/api/estatusOrden', 'EstatusOrdenController');

// Rutas de Ordenes
Route::resource('api/orden', 'OrdenController');

// Rutas de Boletos
Route::resource('api/boleto', 'BoletoController');
Route::get('api/boletos/publicados', 'BoletoController@publicados');

// Rutas de Categorias de Productos
Route::resource('api/categoriaProducto', 'CategoriaProductoController');

// Rutas de Estatus de Productos
Route::resource('api/estatusProducto', 'EstatusProductoController');

// Rutas de Productos
Route::resource('api/producto', 'ProductoController');