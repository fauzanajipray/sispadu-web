<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Cek role user
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            auth()->logout(); // invalidate token
            return response()->json(['error' => 'Superadmin tidak dapat login di aplikasi ini'], 403);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = auth()->user()->load('position');
        return response()->json([
            'access_token' => $token,
            'user' => $user, // with position
            // 'token_type' => 'bearer',
            // 'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function profile()
    {
        $user = auth()->user()->load('position');
        return response()->json($user);
    }

    public function editProfile(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('customers')->ignore(auth()->user()->id)], // ->where(function (QueryBuilder $query) {$query->where('first_level_parent_id', auth()->user()->first_level_parent_id);})
            'password' => 'nullable|min:8|confirmed',
            // 'phone' => 'required|regex:/^[0-9]+$/|max:255',
            'name' => 'required|min:5|max:255',
            // 'birthday' => 'required|date',
            // 'gender' => 'nullable|in:Male,Female',
            // 'province_id' => 'required|exists:provinces,id',
            // 'city_id' => ['required', Rule::exists('cities', 'id')->where(function(Builder $query){
            //     $query->where('province_id', request()->province_id);
            // })],
            // 'district_id' => ['required',  Rule::exists('districts', 'id')->where(function(Builder $query){
            //     $query->where('city_id', request()->city_id)->where('province_id', request()->province_id);
            // })],
            // 'address' => 'required|max:255',
        ]);

        // $geocodeQuery = GeocodeQuery::create(District::where('id', $request['district_id'])->value('name') . ', ' . City::where('id', $request['city_id'])->value('name') . ', ' . Province::where('id', $request['province_id'])->value('name'))->withData('countrycodes', 'id');
        // $geocode = app('geocoder')->using('nominatim')->geocodeQuery($geocodeQuery)->get();
        // if($geocode->count() == 0){
        //     return response()->json(['errors' => [
        //         'province_id' => [trans('api.address_lat_lng_not_found')],
        //         'city_id' => [trans('api.address_lat_lng_not_found')],
        //         'district_id' => [trans('api.address_lat_lng_not_found')],
        //         'address' => [trans('api.address_lat_lng_not_found')]]
        //     ], 422);
        // }
        // $coordinates = $geocode->first()->getCoordinates();

        $user = auth()->user();
        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            // 'province_id' => $request->province_id,
            // 'city_id' => $request->city_id,
            // 'district_id' => $request->district_id,
            // 'phone' => $request->phone,
            // 'birthday' => $request->birthday,
            // 'gender' => $request->gender,
            // 'address' => $request->address,
            // 'location_latitude' => round($coordinates->getLatitude(), 14),
            // 'location_longitude' => round($coordinates->getLongitude(), 14),
            // 'location_point' => DB::raw("(ST_GeomFromText('POINT(" . round($coordinates->getLongitude(), 14) . " " . round($coordinates->getLatitude(), 14) . ")'))")
        ]);
        if ($request->filled('password')) {
            $user->password = $request->password;
        }
        $user->save();
        $user = auth()->user()->load('position');
        return response()->json($user);
    }

    // public function add(Request $request)
    // {
    //     $request->validate([
    //         'email' => ['required', 'email', 'max:255', Rule::unique('customers')],
    //         'password' => 'required|min:8|confirmed',
    //         'phone' => 'required|regex:/^[0-9]+$/|max:255',
    //         'name' => 'required|min:5|max:255',
    //         'birthday' => 'required|date',
    //         'gender' => 'nullable|in:Male,Female',
    //         // 'province_id' => 'required|exists:provinces,id',
    //         // 'city_id' => ['required',  Rule::exists('cities', 'id')->where(function(Builder $query){
    //         //     $query->where('province_id', request()->province_id);
    //         // })],
    //         // 'district_id' => ['required',  Rule::exists('districts', 'id')->where(function(Builder $query){
    //         //     $query->where('city_id', request()->city_id)->where('province_id', request()->province_id);
    //         // })],
    //         // 'address' => 'required|max:255',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         // $geocodeQuery = GeocodeQuery::create(District::where('id', $request['district_id'])->value('name') . ', ' . City::where('id', $request['city_id'])->value('name') . ', ' . Province::where('id', $request['province_id'])->value('name'))->withData('countrycodes', 'id');
    //         // $geocode = app('geocoder')->using('nominatim')->geocodeQuery($geocodeQuery)->get();
    //         // if($geocode->count() == 0){
    //         //     return response()->json(['errors' => [
    //         //         'province_id' => [trans('api.address_lat_lng_not_found')],
    //         //         'city_id' => [trans('api.address_lat_lng_not_found')],
    //         //         'district_id' => [trans('api.address_lat_lng_not_found')],
    //         //         'address' => [trans('api.address_lat_lng_not_found')]]
    //         //     ], 422);
    //         // }
    //         // $coordinates = $geocode->first()->getCoordinates();

    //         $customer = new Customer();
    //         $customer->name = $request->input('name');
    //         $customer->email = $request->input('email');
    //         $customer->password = $request->input('password');
    //         $customer->phone = $request->input('phone');
    //         $customer->birthday = $request->input('birthday');
    //         $customer->gender = $request->input('gender');
    //         // $customer->province_id = $request->input('province_id');
    //         // $customer->city_id = $request->input('city_id');
    //         // $customer->district_id = $request->input('district_id');
    //         // $customer->address = $request->input('address');
    //         $customer->customer_code = $this->generateCustomerCode();

    //         if ($customer->customer_code === null) {
    //             DB::rollback();
    //             $errors['name'] = 'Cannot add new customer because of daily limit registration customer.';
    //             return $this->redirectStoreCrud($errors);
    //         }

    //         // $customer->location_latitude = round($coordinates->getLatitude(), 14);
    //         // $customer->location_longitude = round($coordinates->getLongitude(), 14);
    //         // $customer->location_point = DB::raw("(ST_GeomFromText('POINT(" . round($coordinates->getLongitude(), 14) . " " . round($coordinates->getLatitude(), 14) . ")'))");

    //         $customer->save();

    //         DB::commit();

    //         $otp = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
    //         Otp::create([
    //             'customer_id' => $customer->id,
    //             'otp' => $otp,
    //             'status' => 'Sent',
    //             'expired_at' => Carbon::now()->addMinutes(10),
    //         ]);

    //         // Mail::to($customer->email)->send(new OtpEmail($customer));

    //         $credentials = request(['email', 'password']);
    //         $token = auth()->attempt($credentials);

    //         // return $customer;
    //         return $this->respondWithToken($token);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }
}
