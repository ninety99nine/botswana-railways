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

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//  GET LIST OF TRIPS
Route::get('/trips', function (Request $request) {
    /** We call the input method without any arguments in order to retrieve
     *  all of the input values as an associative array e.g.
     *
     *  .../trips?from_destination_id=1&to_destination_id=2
     *
     *  Converts into:
     *
     *  [
     *      'from_destination_id' => 2,
     *      'to_destination_id' => 2,
     *      ...
     * ]
     *
     * We then use the inputs to filter our trips
     */
    $searchQuery = $request->input();

    //  Get the date we want to leave otherwise default to nothing
    $departure_date = $searchQuery['departure_date'] ?? null;

    //  Get todays date
    $todays_date = (\Carbon\Carbon::now())->format('Y-m-d');

    //  If we have the departure date specified
    if (isset($departure_date) && !empty($departure_date)) {
        try {
            //  Properly format the given date (If any issues occur use todays date)
            $departure_date = Carbon::createFromFormat('Y-m-d', $departure_date);

            //  Make sure that the departure date provided is greater than the current date
            if ((new \Carbon\Carbon($departure_date) )->getTimestamp() < (new \Carbon\Carbon($todays_date) )->getTimestamp()) {
                //  Return a warning message
                return redirect()->back()->withInput()->with('trips_warning_message', 'You selected a past date, only select todays date or future dates');
            }

            //  If an error occurs
        } catch (Exception $e) {
            $departure_date = null;
        }
    } else {
        $departure_date = null;
    }

    //  If the departure date is available and valid
    if ($departure_date) {
        
        //  Get the trips matching the given start trip and end trip
        $travelClasses = \App\TravelClass::with(['accommodations', 'trips' => function ($query) use ($searchQuery) {
            $query->searchTrip([
                'from_id' => $searchQuery['from_destination_id'],
                'to_id' => $searchQuery['to_destination_id'],
            ]);
        }])->get();

        //  Get the bookings matching the given start trip, end trip and departure date
        $bookings = \App\Booking::searchBooking([
            'from_id' => $searchQuery['from_destination_id'],
            'to_id' => $searchQuery['to_destination_id'],
            'leave_date' => $departure_date,
        ])->with([
            'class', 'accommodation', 'tickets','startDestination', 'endDestination',
            'returnClass', 'returnAccommodation', 'returnStartDestination', 'returnEndDestination'
        ])->get();

        //  Update the trip accommodation details
        foreach ($travelClasses as $travelClass) {
            foreach ($travelClass->accommodations as $index => $accommodation) {
                $number_of_occupied_seats = collect($bookings)->where('class.name', $travelClass['name'])->where('accommodation.name', $accommodation['name'])->sum('number_of_passengers');

                //  Set the number of occupied seats
                $travelClass['accommodations'][$index]['occupied_seats'] = $number_of_occupied_seats;

                //  Get the number of available seats
                $number_of_available_seats = ($accommodation->maximum_seats - $number_of_occupied_seats);

                //  Set the number of available seats
                $travelClass['accommodations'][$index]['available_seats'] = ($number_of_available_seats >= 0) ? $number_of_available_seats : 0;
            }
        }

        //  If we don't have a departure date or its not valid
    } else {
        //  Default to no trips
        $travelClasses = [];

        //  Default to no bookings
        $bookings = [];
    }

    $destinations = \App\Destination::all();

    return view('trips.list', ['travelClasses' => $travelClasses, 'destinations' => $destinations, 'bookings' => $bookings, 'request' => $request]);
})->name('trips');

//  CREATE NEW TRIP FORM
Route::get('/trips/create', function (Request $request) {
    //  Get the destinations
    $destinations = \App\Destination::all();

    return view('trips.create', ['destinations' => $destinations]);
})->name('create-trips');

//  CREATE NEW TRIP
Route::post('/trips', function (Request $request) {
    $standard_class_price = $request->input('standard_class_price');
    $business_class_price = $request->input('business_class_price');
    $first_class_price = $request->input('first_class_price');

    $from_destination_id = $request->input('from_destination_id');
    $to_destination_id = $request->input('to_destination_id');

    $first_leave_at = $request->input('first_leave_at');
    $first_arrive_at = $request->input('first_arrive_at');

    $second_leave_at = $request->input('second_leave_at');
    $second_arrive_at = $request->input('second_arrive_at');

    $class_ids = [1, 2, 3];

    if ($from_destination_id == $to_destination_id) {
        return redirect()->back()->withInput()->with('form_warning_message', 'The start and end destination cannot be the same');
    }

    //  Foreach class id
    foreach ($class_ids as $class_id) {
        //  For Standard Class Pricing
        if ($class_id == 1) {
            //  Normal price for adults
            $adult_price = $standard_class_price;

        //  For Business Class Pricing
        } elseif ($class_id == 2) {
            //  Normal price for adults
            $adult_price = $business_class_price;

        //  For First Class Pricing
        } elseif ($class_id == 3) {
            //  Normal price for adults
            $adult_price = $first_class_price;
        }

        //  10% off for seniors
        $senior_price = $adult_price * (90 / 100);

        //  Half price for children
        $child_price = $adult_price / 2;

        //  Zero for infants
        $infant_price = 0;

        DB::table('trips')->insert([
            'from_destination_id' => $from_destination_id,
            'to_destination_id' => $to_destination_id,
            'class_id' => $class_id,
            'adult_price' => $adult_price,
            'senior_price' => $senior_price,
            'child_price' => $child_price,
            'infant_price' => $infant_price,

            'leave_at' => $first_leave_at,
            'arrive_at' => $first_arrive_at,
            'train_number' => '501',
        ]);

        DB::table('trips')->insert([
            'from_destination_id' => $to_destination_id,
            'to_destination_id' => $from_destination_id,
            'class_id' => $class_id,
            'adult_price' => $adult_price,
            'senior_price' => $senior_price,
            'child_price' => $child_price,
            'infant_price' => $infant_price,

            'leave_at' => $second_leave_at,
            'arrive_at' => $second_arrive_at,
            'train_number' => '502',
        ]);
    }

    //  Get the start destination details
    $from = DB::table('destinations')->find($from_destination_id);

    //  Get the end destination details
    $to = DB::table('destinations')->find($to_destination_id);

    return redirect()->back()->with('form_success_message', 'Trip created successfully ('.$from->name.' to '.$to->name.')');
});
