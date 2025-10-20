<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| REST API Routes - CRUD Operations (Minimalni zahtev: 4+ rute)
|--------------------------------------------------------------------------
*/

// OSNOVNE CRUD RUTE ZA NEKRETNINE
Route::get('/properties', [ApiController::class, 'getProperties']);                    // GET - sve nekretnine
Route::post('/properties', [ApiController::class, 'createProperty']);                 // POST - nova nekretnina
Route::get('/properties/{id}', [ApiController::class, 'showProperty']);               // GET - jedna nekretnina
Route::put('/properties/{id}', [ApiController::class, 'updateProperty']);             // PUT - ažuriraj nekretninu
Route::delete('/properties/{id}', [ApiController::class, 'deleteProperty']);          // DELETE - obriši nekretninu

// DODATNE RUTE ZA PROPERTY TYPES I STATES
Route::get('/property-types', [ApiController::class, 'getPropertyTypes']);            // GET - tipovi nekretnina
Route::get('/states', [ApiController::class, 'getStates']);                           // GET - države/regioni

/*
|--------------------------------------------------------------------------
| Nested Routes - Ugnježdene rute (Zahtev za višu ocenu: 2+ rute)
|--------------------------------------------------------------------------
*/

// UGNJEŽDENE RUTE - /users/{id}/properties
Route::get('/users/{user_id}/properties', [ApiController::class, 'getUserProperties']); // Nekretnine određenog korisnika

// UGNJEŽDENE RUTE - /properties/{id}/images  
Route::get('/properties/{property_id}/images', [ApiController::class, 'getPropertyImages']); // Slike određene nekretnine

/*
|--------------------------------------------------------------------------
| External Web Services - Javni veb servisi (Minimalni zahtev: 1+ servis)
|--------------------------------------------------------------------------
*/

// JAVNI VEB SERVIS 1 - OpenStreetMap Nominatim API za geolokaciju
Route::get('/properties/{property_id}/location', [ApiController::class, 'getPropertyLocation']); // Lokacija nekretnine

// JAVNI VEB SERVIS 2 - ExchangeRate API za konverziju valuta  
Route::get('/properties/{property_id}/convert/{currency?}', [ApiController::class, 'convertPropertyPrice']); // Konverzija cena

/*
|--------------------------------------------------------------------------
| API Documentation Routes (za lakše testiranje)
|--------------------------------------------------------------------------
| 
| GET    /api/properties              - Lista svih nekretnina
| POST   /api/properties              - Kreiranje nove nekretnine
| GET    /api/properties/{id}         - Detalji određene nekretnine
| PUT    /api/properties/{id}         - Ažuriranje nekretnine
| DELETE /api/properties/{id}         - Brisanje nekretnine
| GET    /api/property-types          - Lista tipova nekretnina
| GET    /api/states                  - Lista država/regiona
| GET    /api/users/{user_id}/properties        - Nekretnine određenog korisnika
| GET    /api/properties/{id}/images            - Slike određene nekretnine
| GET    /api/properties/{id}/location          - Geolokacija nekretnine
| GET    /api/properties/{id}/convert/{currency} - Konverzija cene u drugu valutu
|
*/
