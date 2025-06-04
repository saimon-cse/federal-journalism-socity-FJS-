<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila; // Ensure this is imported if not already
use Illuminate\Http\JsonResponse; // For type hinting

class GeographyController extends Controller
{
    /**
     * Get districts for a given division.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistricts(Division $division): JsonResponse
    {
        // Eager load is not strictly necessary here as we are selecting specific fields,
        // but good practice if you were returning the full model.
        $districts = $division->districts()->orderBy('name_en')->get(['id', 'name_en']);
        return response()->json($districts);
    }

    /**
     * Get upazilas for a given district.
     *
     * @param  \App\Models\District  $district
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpazilas(District $district): JsonResponse
    {
        $upazilas = $district->upazilas()->orderBy('name_en')->get(['id', 'name_en']);
        return response()->json($upazilas);
    }

    /**
     * Get all divisions (optional, if needed for an initial population elsewhere)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDivisions(): JsonResponse
    {
        $divisions = Division::orderBy('name_en')->get(['id', 'name_en']);
        return response()->json($divisions);
    }
}
