<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Train Trips</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                font-family: 'Nunito', sans-serif;
                background-color: #fff;
                font-weight: 200;
                height: 100vh;
                color: #000;
                margin: 0;
            }

            .full-height {
                height: 200vh;
            }

            .flex-center {
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .content {
                width: 90%;
                text-align: center;
                padding-top: 30px;
            }

            .title {
                font-size: 84px;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                padding: 10px;
                border: 1px solid grey;
                border-collapse: collapse;
            }

            table thead tr th {
                border-bottom: 1px solid grey;
                padding-bottom: 10px;
                padding-top: 10px;
                text-align: left;
                color: #000;
            }

            table tbody tr th {
                text-align: left;
                font-weight: 100;
                color: #000;
            }

            table tbody tr:hover {
                background: #9adaff;
            }

            table#trip_search thead tr th:nth-child(1),
            table#trip_search tbody tr th:nth-child(1),
            table#trip_list thead tr th:nth-child(1),
            table#trip_list tbody tr th:nth-child(1) {
                padding-left: 10px;
            }

            .text-center {
                text-align: center;
            }

            .text-bold {
                font-weight:bold;
            }

            .info-msg,
            .success-msg,
            .warning-msg,
            .error-msg {
              margin: 10px 0;
              padding: 10px;
              border-radius: 3px 3px 3px 3px;
            }
            .info-msg {
              color: #059;
              background-color: #BEF;
            }
            .success-msg {
              color: #270;
              background-color: #DFF2BF;
            }
            .warning-msg {
              color: #9F6000;
              background-color: #FEEFB3;
            }
            .error-msg {
              color: #D8000C;
              background-color: #FFBABA;
            }
            
            .passenger_ticket {
                margin: 8px;
                display: block;
                border-radius: 2px;
                background: #f7f7f7;
                padding: 10px 5px 10px 10px;
                box-shadow: 1px 3px 3px #dcdcdc;
                transition: all 0.3s ease;
            }

            .passenger_ticket:hover {
                cursor: pointer;    
                background: #fff;
                transition: all 0.3s ease;
                padding: 15px 5px 15px 10px;
            }

            .adult_ticket {
                border-left: 10px solid #7fb0e2;
            }

            .senior_ticket {
                border-left: 10px solid #eadf55;
            }

            .child_ticket {
                border-left: 10px solid #6cde6a;
            }

            .passenger_info {
                
            }

            .passenger_ticket_details{
                border-top: 1px dashed transparent;
                transition: height 0.5s ease;
                line-height: 1.5em;
                overflow: hidden;
                height: 0px;
            }

            .passenger_ticket:hover > .passenger_ticket_details{
                border-top: 1px dashed #afafaf;
                transition: height 0.5s ease;
                padding-top: 10px;
                margin-top: 15px;
                height: 90px;
            }

            .table-row-border-left{
                border-left: 1px dashed #bbbbbb;
            }

            .w-50 {
                width:50%;
            }

            .w-100 {
                width:100%;
            }
            
            .d-flex {
                display: flex;
            }

        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">
                
                <div class="d-flex">
                    <a href="{{ route('trips') }}">Trips</a>
                    <a href="{{ route('create-trips') }}">Create Trips</a>
                </div>

                <div class="title m-b-md">
                    Train Trips
                </div>
                
                @if(session()->has('trips_warning_message'))
                    <div class="warning-msg">
                        {{ session()->get('trips_warning_message') }}
                    </div>
                @endif

                <form method="GET" action="/trips">
                    @csrf

                    <table id="trip_search">
                        <thead>
                            <tr>
                                <th colspan="4">
                                    <label for="from_destination_id">From:</label>
                                    <select name="from_destination_id" required>
                                        @foreach ($destinations as $destination)
                                            @if( $destination->id == $request->input('from_destination_id') )
                                                <option value="{{ $destination->id }}" selected>{{ $destination->name }}</option>
                                            @else
                                                <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </th>
                                <th colspan="4">
                                    <label for="to_destination_id">To:</label>
                                    <select name="to_destination_id" style="margin-right: 20px;" required>
                                        @foreach ($destinations as $destination)
                                        @if( $destination->id == $request->input('to_destination_id') )
                                                <option value="{{ $destination->id }}" selected>{{ $destination->name }}</option>
                                            @else
                                                <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </th>
                                <th colspan="3">
                                    <label for="departure_date">Select Date:</label>
                                    <input type="date" name="departure_date" value="{{ $request->input('departure_date') }}" style="margin: 10px 0;" required>
                                </th>
                                <th colspan="2">
                                    <input type="submit" value="Search" style="padding: 5px 10px;cursor: pointer;">
                                </th>
                            </tr>
                        </thead>
                    </table>

                </form>

                <table id="trip_list">
                    <thead>
                        <tr>
                            <th colspan="1">From</th>
                            <th colspan="1">To</th>
                            <th colspan="1">Class</th>
                            <th colspan="1"></th>
                            <th colspan="1">On</th>
                            <th colspan="1">Leaving At</th>
                            <th colspan="1">Arrival Date</th>
                            <th colspan="1">Arrving At</th>
                            <th colspan="1">Train Nr</th>
                            <th colspan="1">Seats</th>
                            <th colspan="1">Adult</th>
                            <th colspan="1">Senior</th>
                            <th colspan="1">Child</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ( count( $travelClasses ) )
        
                            @foreach ($travelClasses as $travelClass)

                                @foreach ($travelClass->trips as $trip)

                                    @foreach ($travelClass->accommodations as $accommodation)

                                        <tr>
                                            <th colspan="1">{{ $trip->from_destination->name }}</th>
                                            <th colspan="1">{{ $trip->to_destination->name }}</th>
                                            <th colspan="1">{{ $travelClass->name }}</th>
                                            <th colspan="1">{{ $accommodation->name }}</th>
                                            <th colspan="1"></th>
                                            <th colspan="1">{{ $trip->leave_at }}</th>
                                            <th colspan="1"></th>
                                            <th colspan="1">{{ $trip->arrive_at }}</th>
                                            <th colspan="1">{{ $trip->train_number }}</th>
                                            <th colspan="1">{{ $accommodation->available_seats }} / {{ $accommodation->maximum_seats }}</th>
                                            <th colspan="1">{{ number_format($trip->adult_price, 2, '.', ',') }}</th>
                                            <th colspan="1">{{ number_format($trip->senior_price, 2, '.', ',') }}</th>
                                            <th colspan="1">{{ number_format($trip->child_price, 2, '.', ',') }}</th>
                                        </tr>
                                        
                                    @endforeach

                                @endforeach

                            @endforeach

                        @else

                            <tr>
                                <th colspan="13">
                                    <div class="info-msg text-center">No trips available</div>
                                </th>
                            </tr>

                        @endif
                    </tbody>
                </table>

                <div class="title m-b-md" style="margin-top:50px;">
                    Bookings
                </div>

                <table id="trip_list">
                    <thead>
                        <tr>
                            <th colspan="1">ID</th>
                            <th colspan="1">Class</th>
                            <th colspan="1">Accommodation</th>
                            <th colspan="1">Passengers</th>
                            <th colspan="1" class="table-row-border-left" style="padding-left: 15px;">Passengers Details</th>
                            <th colspan="1" class="table-row-border-left" style="padding-left: 15px;">Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ( count( $bookings ) )

                            @foreach ($bookings as $booking)

                                <tr style="border: 1px solid grey;">
                                    <th colspan="1">#{{ $booking->id }}</th>
                                    <th colspan="1">{{ $booking->class->name }}</th>
                                    <th colspan="1">{{ $booking->accommodation->name }}</th>
                                    <th colspan="1">{{ $booking->number_of_passengers }}</th>
                                    <th colspan="1" class="table-row-border-left" style="padding: 0 15px;">
                                        @foreach ($booking->tickets as $ticket)
                                            <span class="passenger_ticket {{ strtolower($ticket['passenger_type']).'_ticket' }}">
                                                <span class="passenger_info">{{ $ticket['full_name'] }} ({{ $ticket['passenger_type'] }})</span>
                                                @if( !empty($ticket['identity_type']) && $ticket['identity_type'] != 'none' )
                                                    - <span class="passenger_info">{{ $ticket['identity_type'] }}: {{ $ticket['identity_no'] }}</span>
                                                @endif

                                                <div class="passenger_ticket_details">
                                                    
                                                    <div class="d-flex">

                                                        <div class="w-50">
                                                            <div>
                                                                Ticket Reference:
                                                                <span class="text-bold">#{{ $ticket->reference_no }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="w-50">
                                                            <div>
                                                                Train No:
                                                                <span class="text-bold">{{ $booking->train_number }}</span> 
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="d-flex">
                                                    
                                                        <div class="w-50">
                                                            <div>
                                                                Departure:
                                                                <span class="text-bold">
                                                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $booking->leave_datetime)->format('D - d M Y H:i') }}    
                                                                </span> 
                                                            </div>
                                                            <div>
                                                                Travel From:
                                                                <span class="text-bold">{{ $booking->startDestination->name }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="w-50">
                                                            <div>
                                                                Arrive:
                                                                <span class="text-bold">
                                                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $booking->arrive_datetime)->format('D - d M Y H:i') }}    
                                                                </span> 
                                                            </div>
                                                            <div>
                                                                Travel To:
                                                                <span class="text-bold">{{ $booking->endDestination->name }}</span> 
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </span>
                                        @endforeach
                                    </th>
                                    <th colspan="1" class="table-row-border-left" style="padding-left: 15px;">
                                        P{{ number_format($booking->payment_amount, 2, '.', ',') }}
                                    </th>
                                </tr>
                                
                            @endforeach

                        @else

                            <tr>
                                <th colspan="13">
                                    <div class="info-msg text-center">No booked trips</div>
                                </th>
                            </tr>

                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
