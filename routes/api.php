<?php

use Carbon\Carbon;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

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
        ])->get();

        //  Update the trip accommodation details
        foreach ($travelClasses as $travelClass) {
            foreach ($travelClass->accommodations as $index => $accommodation) {
                $number_of_occupied_seats = collect($bookings)->where('class', $travelClass['name'])->where('accommodation', $accommodation['name'])->sum('number_of_passengers');

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
    }

    $destinations = \App\Destination::all();

    return ['travelClasses' => $travelClasses, 'destinations' => $destinations, 'request' => $request];

    //  return view('trips', ['travelClasses' => $travelClasses, 'destinations' => $destinations, 'request' => $request]);
});

Route::get('/destinations', function (Request $request) {
    //  Get the ID's of the destinations to exclude from the search
    $excludedIds = $request->input('excluded_ids');
    $excludedIds = explode(',', $excludedIds);
    $excludedIds = collect($excludedIds)->map(function ($excludedId) {
        return trim($excludedId);
    });

    $destinations = \App\Destination::whereNotIn('id', $excludedIds)->get();

    return $destinations;
});

//  CREATE NEW BOOKING
Route::post('/booking', function (Request $request) {
    $msisdn = $request->input('msisdn');
    $class_id = $request->input('class_id');
    $accommodation_id = $request->input('accommodation_id');
    $from_destination_id = $request->input('from_destination_id');
    $to_destination_id = $request->input('to_destination_id');
    $departure_date = $request->input('departure_date');
    $passengers = $request->input('passengers');

    //	Travel Class
    $travel_class = \App\TravelClass::find($class_id);

    //  Trip
    $trip = $travel_class->trips()->where('from_destination_id', $from_destination_id)
                                  ->where('to_destination_id', $to_destination_id)
                                  ->first();
    //  Trip Details
    $leave_at = $trip->leave_at;
    $arrive_at = $trip->arrive_at;
    $train_number = $trip->train_number;
    $to_destination = $trip->to_destination->name;
    $from_destination = $trip->from_destination->name;

    //	Trip Pricing Details
    $adult_price = $trip->adult_price;
    $senior_price = $trip->senior_price;
    $child_price = $trip->child_price;
    $infant_price = $trip->infant_price;

    //  Get the departure datetime
    $leave_datetime = $departure_date.' '.$leave_at.':00';

    //  Get the arrival datetime
    $arrive_datetime = $departure_date.' '.$arrive_at.':00';

    //  If the arrival datetime is less than the leaving datetime
    if (\Carbon\Carbon::parse($leave_datetime)->getTimestamp() > \Carbon\Carbon::parse($arrive_datetime)->getTimestamp()) {
        //  Add a day to the arrival datetime so that we arrive later not before the arrival datetime
        $arrive_datetime = \Carbon\Carbon::parse($arrive_datetime)->addDay()->format('Y-m-d H:i:s');
    }

    //	Passenger Details
    $total_passengers = collect($passengers)->count();
    $number_of_adults = collect($passengers)->where('type', 'adult')->count();
    $number_of_seniors = collect($passengers)->where('type', 'senior')->count();
    $number_of_children = collect($passengers)->where('type', 'child')->count();
    $number_of_infants = collect($passengers)->where('type', 'infant')->count();

    $total_passengers_in_words = '';

    //	Record the Adult quantity on the passengers list
    if ($number_of_adults >= 1) {
        $result = ($number_of_adults == 1)
                         ? $number_of_adults.' Adult' : $number_of_adults.'Adults';
        if (empty($total_passengers_in_words)) {
            $total_passengers_in_words .= $result;
        } else {
            $total_passengers_in_words .= ', '.$result;
        }
    }

    //	Record the Senior quantity on the passengers list
    if ($number_of_seniors >= 1) {
        $result = ($number_of_seniors == 1)
                         ? $number_of_seniors.' Senior' : $number_of_seniors.'Seniors';
        if (empty($total_passengers_in_words)) {
            $total_passengers_in_words .= $result;
        } else {
            $total_passengers_in_words .= ', '.$result;
        }
    }

    //	Record the Child quantity on the passengers list
    if ($number_of_children >= 1) {
        $result = ($number_of_children == 1)
                         ? $number_of_children.' Child' : $number_of_children.'Children';
        if (empty($total_passengers_in_words)) {
            $total_passengers_in_words .= $result;
        } else {
            $total_passengers_in_words .= ', '.$result;
        }
    }

    //	Record the Infant quantity on the passengers list
    if ($number_of_infants >= 1) {
        $result = ($number_of_infants == 1)
                         ? $number_of_infants.' Infant' : $number_of_infants.'Infants';
        if (empty($total_passengers_in_words)) {
            $total_passengers_in_words .= $result;
        } else {
            $total_passengers_in_words .= ', '.$result;
        }
    }

    //	Calculate the trip costs
    $total_price = 0;
    $total_price += $number_of_adults * $adult_price;
    $total_price += $number_of_seniors * $senior_price;
    $total_price += $number_of_children * $child_price;
    $total_price += $number_of_infants * $infant_price;

    $booking = \App\Booking::create([
        'msisdn' => $msisdn,
        'class_id' => $class_id,
        'accommodation_id' => $accommodation_id,
        'start_destination_id' => $from_destination_id,
        'end_destination_id' => $to_destination_id,
        'leave_datetime' => $leave_datetime,
        'arrive_datetime' => $arrive_datetime,
        /*
        'wants_to_return' => $wants_to_return,
        'wants_to_return' => $wants_to_return,
        'return_class_id' => $return_class_id,
        'return_accommodation_id' => $return_accommodation_id,
        'return_start_destination_id' => $return_start_destination_id,
        'return_end_destination_id' => $return_end_destination_id,
        'return_leave_datetime' => $return_leave_datetime,
        'return_arrive_datetime' => $return_arrive_datetime,
        'train_number' => $train_number,
        'return_train_number' => $return_train_number,
        */

        'payment_amount' => $total_price,

        'number_of_passengers' => $total_passengers,
        'number_of_adults' => $number_of_adults,
        'number_of_seniors' => $number_of_seniors,
        'number_of_children' => $number_of_children,
        'number_of_infants' => $number_of_infants,

        'train_number' => $train_number,
        /*
        'emergency_contact_name' => $emergency_contact_name,
        'emergency_contact_phone' => $emergency_contact_phone
        */
    ]);

    $tickets = [];

    foreach ($passengers as $key => $passenger) {
        array_push($tickets,
            new \App\Ticket([
                'full_name' => $passenger['first_name'].' '.$passenger['last_name'],
                'identity_no' => $passenger['identity_number'],
                'identity_type' => $passenger['identity_type'],
                'passenger_type' => $passenger['type'],
                'reference_no' => time() + $key,
            ])
        );
    }

    $booking->tickets()->saveMany($tickets);

    $booking = $booking->load('tickets');

    return response($booking, 200)->header('Content-Type', 'application/json');
});

//  GET EXISTING BOOKINGS FOR A GIVEN MSISDN
Route::get('/{msisdn}/bookings', function (Request $request, $msisdn) {
    return \App\Booking::with(['accommodation', 'startDestination', 'endDestination', 'tickets'])->where('msisdn', $msisdn)->get();
});

//  GET EXISTING BOOKING FOR A GIVEN MSISDN
Route::get('/{msisdn}/bookings/{booking_id}', function (Request $request, $msisdn, $booking_id) {
    return \App\Booking::with('tickets')->where(['id' => $booking_id, 'msisdn' => $msisdn])->get();
});
