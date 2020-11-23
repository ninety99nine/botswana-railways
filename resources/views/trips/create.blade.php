<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Create Trip</title>

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

            table#trip_form thead tr th,
            table#trip_form tbody tr th {
                text-align: right;
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
                    Create Trip
                </div>

                @if(session()->has('form_success_message'))
                    <div class="success-msg">
                        {{ session()->get('form_success_message') }}
                    </div>
                @endif

                @if(session()->has('form_warning_message'))
                    <div class="warning-msg">
                        {{ session()->get('warning_message') }}
                    </div>
                @endif
                
                <form method="POST" action="/trips">
                    @csrf
                    <table id="trip_form">
                        <thead>
                            <tr>
                                <th colspan="2">
                                    <label for="from">From:</label>
                                    <select name="from_destination_id" required>
                                        @foreach ($destinations as $destination)
                                            @if($destination->id == old('from_destination_id'))
                                                <option value="{{ $destination->id }}" selected>{{ $destination->name }}</option>
                                            @else
                                                <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </th>
                                <th colspan="1">
                                    <label for="to">To:</label>
                                    <select name="to_destination_id" style="margin-right: 20px;" required>
                                        @foreach ($destinations as $destination)
                                            @if($destination->id == old('to_destination_id'))
                                                <option value="{{ $destination->id }}" selected>{{ $destination->name }}</option>
                                            @else
                                                <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th colspan="1">
                                    <label for="standard_class_price">Standard Class Price:</label>
                                    <input type="number" name="standard_class_price" value="{{ old('standard_class_price') }}" style="margin: 40px 0;" required>
                                </th>
                                <th colspan="1">
                                    <label for="business_class_price">Business Class Price:</label>
                                    <input type="number" name="business_class_price" value="{{ old('business_class_price') }}" style="margin: 40px 0;" required>
                                </th>
                                <th colspan="1">
                                    <label for="first_class_price">First Class Price:</label>
                                    <input type="number" name="first_class_price" value="{{ old('first_class_price') }}" style="margin: 40px 20px 40px 0;" required>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="1">
                                    <label for="first_leave_at">First Trip (Leave At):</label>
                                    <input type="time" name="first_leave_at" value="{{ old('first_leave_at') }}" style="margin: 10px 0;" required>
                                </th>
                                <th colspan="1">
                                    <label for="first_arrive_at">First Trip (Arrive At):</label>
                                    <input type="time" name="first_arrive_at" value="{{ old('first_arrive_at') }}" style="margin: 10px 0;" required>
                                </th>
                                <th colspan="1"></th>
                            </tr>
                            <tr>
                                <th colspan="1">
                                    <label for="second_leave_at">Second Trip (Leave At):</label>
                                    <input type="time" name="second_leave_at" value="{{ old('second_leave_at') }}" style="margin: 10px 0;" required>
                                </th>
                                <th colspan="1">
                                    <label for="second_arrive_at">Second Trip (Arrive At):</label>
                                    <input type="time" name="second_arrive_at" value="{{ old('second_arrive_at') }}" style="margin: 10px 0;" required>
                                </th>
                                <th colspan="1"></th>
                            </tr>
                            <tr>
                                <th colspan="2"></th>
                                <th colspan="1">
                                    <input type="submit" value="Create Trip" style="margin: 20px;padding: 10px 40px;cursor: pointer;">
                                </th>
                            </tr>    
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </body>
</html>
