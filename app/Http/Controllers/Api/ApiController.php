<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\User;
use App\Models\MultiImage;
use App\Models\PropertyType;
use App\Models\State;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * GET - Sve nekretnine
     * API endpoint: /api/properties
     */
    public function getProperties()
    {
        try {
            $properties = Property::with(['type', 'user', 'pstate'])
                ->where('status', '1')
                ->get();
                
            return response()->json([
                'success' => true,
                'message' => 'Properties retrieved successfully',
                'count' => $properties->count(),
                'data' => $properties
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving properties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST - Kreiranje nove nekretnine
     * API endpoint: /api/properties
     */
    public function createProperty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_name' => 'required|string|max:255',
            'ptype_id' => 'required|integer|exists:property_types,id',
            'agent_id' => 'required|integer|exists:users,id',
            'property_code' => 'required|string|unique:properties',
            'property_status' => 'required|string',
            'lowest_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Korišćenje DB transakcije za bezbednost
            DB::beginTransaction();

            $property = Property::create([
                'property_name' => $request->property_name,
                'property_slug' => strtolower(str_replace(' ', '-', $request->property_name)),
                'ptype_id' => $request->ptype_id,
                'agent_id' => $request->agent_id,
                'property_code' => $request->property_code,
                'property_status' => $request->property_status,
                'lowest_price' => $request->lowest_price,
                'max_price' => $request->max_price,
                'property_thambnail' => 'default.jpg',
                'amenities_id' => $request->amenities_id ?? '1',
                'short_descp' => $request->short_descp,
                'long_descp' => $request->long_descp,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'garage' => $request->garage,
                'property_size' => $request->property_size,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'status' => '0', // Default neaktivno dok admin ne odobri
                'created_at' => Carbon::now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully',
                'data' => $property->load(['type', 'user', 'pstate'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET - Jedna specifična nekretnina
     * API endpoint: /api/properties/{id}
     */
    public function showProperty($id)
    {
        try {
            $property = Property::with(['type', 'user', 'pstate'])
                ->find($id);
            
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Property retrieved successfully',
                'data' => $property
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT - Ažuriranje postojeće nekretnine
     * API endpoint: /api/properties/{id}
     */
    public function updateProperty(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'property_name' => 'sometimes|required|string|max:255',
            'ptype_id' => 'sometimes|required|integer|exists:property_types,id',
            'lowest_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $property = Property::find($id);
            
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            // DB transakcija za sigurno ažuriranje
            DB::beginTransaction();

            $updateData = $request->only([
                'property_name', 'ptype_id', 'property_status', 'lowest_price', 
                'max_price', 'short_descp', 'long_descp', 'bedrooms', 'bathrooms',
                'garage', 'property_size', 'address', 'city', 'state', 'postal_code'
            ]);

            // Ako se menja ime, ažuriraj i slug
            if ($request->has('property_name')) {
                $updateData['property_slug'] = strtolower(str_replace(' ', '-', $request->property_name));
            }

            $property->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully',
                'data' => $property->load(['type', 'user', 'pstate'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE - Brisanje nekretnine
     * API endpoint: /api/properties/{id}
     */
    public function deleteProperty($id)
    {
        try {
            $property = Property::find($id);
            
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            // DB transakcija za sigurno brisanje
            DB::beginTransaction();

            // Obriši povezane slike
            MultiImage::where('property_id', $id)->delete();
            
            // Obriši glavnu nekretninu
            $property->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Property and related images deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UGNJEŽDENA RUTA 1: Nekretnine određenog korisnika/agenta
     * API endpoint: /api/users/{user_id}/properties
     */
    public function getUserProperties($user_id)
    {
        try {
            $user = User::find($user_id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $properties = Property::where('agent_id', $user_id)
                ->with(['type', 'pstate'])
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'User properties retrieved successfully',
                'data' => [
                    'user' => $user->only(['id', 'name', 'email', 'role']),
                    'properties_count' => $properties->count(),
                    'properties' => $properties
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user properties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UGNJEŽDENA RUTA 2: Slike određene nekretnine
     * API endpoint: /api/properties/{property_id}/images
     */
    public function getPropertyImages($property_id)
    {
        try {
            $property = Property::find($property_id);
            
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            $images = MultiImage::where('property_id', $property_id)->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Property images retrieved successfully',
                'data' => [
                    'property' => $property->only(['id', 'property_name', 'property_thambnail']),
                    'images_count' => $images->count(),
                    'images' => $images
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving property images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * JAVNI VEB SERVIS 1: Google Maps Geocoding API za lokaciju nekretnine
     * API endpoint: /api/properties/{property_id}/location
     */
    public function getPropertyLocation($property_id)
    {
        try {
            $property = Property::find($property_id);
            
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            if (!$property->address || !$property->city) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property address is incomplete'
                ], 400);
            }

            // Poziv Google Maps Geocoding API
            $address = $property->address . ', ' . $property->city . ', Serbia';

            // BESPLATNA ALTERNATIVA - OpenStreetMap Nominatim API
            $response = Http::get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1
            ]);

            if ($response->successful() && !empty($response->json())) {
                $locationData = $response->json()[0];
                
                return response()->json([
                    'success' => true,
                    'message' => 'Location data retrieved successfully',
                    'data' => [
                        'property' => $property->only(['id', 'property_name', 'address', 'city']),
                        'coordinates' => [
                            'latitude' => $locationData['lat'],
                            'longitude' => $locationData['lon']
                        ],
                        'formatted_address' => $locationData['display_name'],
                        'api_source' => 'OpenStreetMap Nominatim'
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not geocode the address',
                    'property' => $property->only(['id', 'property_name', 'address', 'city'])
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving location data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * JAVNI VEB SERVIS 2: Currency Exchange API za konverziju cena
     * API endpoint: /api/properties/{property_id}/convert/{currency}
     */
    public function convertPropertyPrice($property_id, $currency = 'EUR')
    {
        try {
            $property = Property::find($property_id);
            
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            if (!$property->lowest_price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property has no price set'
                ], 400);
            }

            // Poziv besplatnog Currency Exchange API
            $response = Http::get("https://api.exchangerate-api.com/v4/latest/RSD");
            
            if ($response->successful()) {
                $exchangeData = $response->json();
                $rates = $exchangeData['rates'];
                
                if (!isset($rates[$currency])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Currency not supported',
                        'supported_currencies' => array_keys($rates)
                    ], 400);
                }
                
                $convertedPrice = $property->lowest_price * $rates[$currency];
                $convertedMaxPrice = $property->max_price ? $property->max_price * $rates[$currency] : null;
                
                return response()->json([
                    'success' => true,
                    'message' => 'Currency conversion completed',
                    'data' => [
                        'property' => $property->only(['id', 'property_name']),
                        'original_currency' => 'RSD',
                        'target_currency' => $currency,
                        'exchange_rate' => $rates[$currency],
                        'prices' => [
                            'original' => [
                                'lowest_price' => $property->lowest_price . ' RSD',
                                'max_price' => $property->max_price ? $property->max_price . ' RSD' : null
                            ],
                            'converted' => [
                                'lowest_price' => round($convertedPrice, 2) . ' ' . $currency,
                                'max_price' => $convertedMaxPrice ? round($convertedMaxPrice, 2) . ' ' . $currency : null
                            ]
                        ],
                        'last_updated' => $exchangeData['date'],
                        'api_source' => 'ExchangeRate-API'
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve exchange rates',
                    'error' => 'Currency API unavailable'
                ], 503);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error converting currency',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DODATNA RUTA: GET svi tipovi nekretnina
     * API endpoint: /api/property-types
     */
    public function getPropertyTypes()
    {
        try {
            $types = PropertyType::all();
            
            return response()->json([
                'success' => true,
                'message' => 'Property types retrieved successfully',
                'data' => $types
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving property types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DODATNA RUTA: GET sve države/regione
     * API endpoint: /api/states
     */
    public function getStates()
    {
        try {
            $states = State::all();
            
            return response()->json([
                'success' => true,
                'message' => 'States retrieved successfully',
                'data' => $states
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving states',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}