<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmiTrack</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
            color: #333;
        }

        /* Navigation */
        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px 50% solid #292929;
        }
        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #2d8a64;
            text-decoration: none;
            margin-right: 1rem;
        }

        .navbar .logo img {
            height: 40px;
            width: auto;
        }

        .navbar nav a {
            
            text-decoration: none;
            color: #555;
            margin-left: 1.5rem;
            font-weight: 500;
        }
        .navbar nav a:hover {
            color: #000;
        }

        .disclaimer {
            text-align: center;
            text-align-last: center; 
            text-justify: inter-word;
            font-size: 1rem;
            color: #000000;
            opacity: 0.5;
            margin-top: 1rem;
            font-style: normal;
        }



/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */

        /* General Container */
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .container-info-button {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
            align-items: center;
            text-align: center;
            justify-content: space-between;
            display: flex;
            gap: 5px;
        }
        .container-calculate {
            background-color:  #e6f9f0;
            height: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
            align-content: center;
        }
        .container-info-button-result {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
            text-align: center;
            justify-content: center;
            display: flex;
            gap: 5px;
        }

/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */

        /* Button */
        .btn {
            display: inline-block;
            background-color: #34a853;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover, .btn-info:hover {
            background-color: #2d8a64;
        }
        .btn-info {
            display: inline-block;
            background-color: #34a853;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            align-content: center;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            width: 250px;
            height: 40px;
        }

/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */

        /* Homepage Sections */
        .banner {
            background-color: #e6f9f0; /* Light green */
            border-radius: 12px;
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }
        .banner h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1.5rem;
        }
        .info-section {
            display: flex;
            gap: 2rem;
            align-items: center;
            margin-bottom: 3rem;
            background-color: #ffffff;
            border-radius: 25px;
        }
        .info-section .text {
            flex: 1;
            text-align: justify;
            padding-left: 20px;
            padding-right: 20px;
        }
        .info-section .image-container {
            flex: 1;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }
        .info-section .image {
            flex: 1;
            background-color: #ffffff;
            border-radius: 8px;
            max-height: 250px;
            width: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }
        .info-section:nth-child(even) {
            flex-direction: row-reverse;
        }

/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */

        /* Calculate Form */
        .form-box {
            max-width: 450px;
            margin: 3rem auto;
            padding: 2.5rem;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .form-box h2 {
            text-align: center;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box; /* Important for 100% width */
        }
        .form-group .btn {
            width: 100%;
            text-align: center;
        }

/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */

        /* Result Page Styling */
        .container-result {
            width: 100%;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* New */
        .result-box {
            background-color: #e6f9f0;
            border-radius: 12px;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            width: 100%;
            max-width: 2000px;
            margin: 2rem auto;
            box-sizing: border-box;
        }

        /* good */
        .result-box h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
            margin-top: 0;
        }

        /* New */
        .map-container {
            flex: 1;
            aspect-ratio: 4/3;
            min-width: 300px;
            margin-right: 5rem;
        }
        
        /* New */
        #map {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* new */
        .data-container {
            flex: 1;
            text-align: center;
        }

        /* new */
        .data-container h1 {
            margin-top: 0;
            font-size: 1.8rem;
            color: #333;
        }

        /* new */
        .emission-value {
            font-size: 3rem;
            font-weight: bold;
            color: #2d8a64;
            margin: 1rem 0 2rem 0;
        }

        /* new */
        .result-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            text-align: left;
            margin-bottom: 2rem;
        }

        /* new */
        .result-bottom {
            border-top: 1px solid #cce8dc;
            padding-top: 2rem;
        }
        
        /* new */
        .label { font-weight: 600; color: #555; font-size: 0.9rem; }
        .value { font-weight: 500; font-size: 1.1rem; color: #333; }

        /* new */
        .recommendation-section {
            margin-top: 2rem;
            border-top: 1px solid #cce8dc; /* Separator line */
            padding-top: 2rem;
        }

        /* new */
        .rec-message {
            text-align: center;
            text-align-last: center; 
            text-justify: inter-word;
            font-size: 1rem;
            color: #444;
            margin-top: 1.5rem;
            font-style: italic;
        }

        /* new */
        .rec-cards-container {
            display: flex;
            justify-content: center; /* Centers 1 item, or centers the group of 2 */
            gap: 1.5rem; /* Space between the two cards */
            flex-wrap: wrap;
            align-items: flex-start;
        }

        /* new */
        .rec-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            flex: 1 1 250px; /* Don't grow too wide, start at 300px */
            text-align: left;
            border-left: 5px solid #34a853;
            max-width: 350px;
        }

        /* Mobile Responsive: Stack them on small screens */
        @media (max-width: 900px) {
            .result-box {
                flex-direction: column-reverse; /* Put map on bottom or top as you prefer */
            }
            .map-container {
                width: 100%;
                height: 300px;
                margin-right: 0;
            }
            #map {
                height: 300px;
            }
        }
        
        /* Hide the text instructions from the routing plugin (optional) */
        .leaflet-routing-container {
            display: none;
        }

        /* Your grid fix from before */
        .result-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            text-align: left;
            gap: 1.5rem;
        }

        /* Custom Directions Box (Floating on the map) */
        .directions-box {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 300px;
            max-height: 450px;
            background: white;
            z-index: 1000; /* Sit on top of map */
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            overflow-y: auto; /* Scrollable if long */
            font-size: 0.9rem;
            font-family: sans-serif;
        }
        .directions-box h3 {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .step-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #f9f9f9;
            padding-bottom: 5px;
        }
        .step-icon {
            font-weight: bold;
            color: #2d8a64;
            min-width: 20px;
        }
        
        .map-section {
            position: relative; 
        }

/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */

        /* Mobile adjustment */
        @media (max-width: 600px) {
            .result-details {
                grid-template-columns: 1fr;
            }
            .result-bottom {
                grid-template-columns: 1fr;
            }
            .info-section {
                flex-direction: column !important;
            }
            .result-details{
                text-align: center;
            }
            .container-info-button {
            align-items: center;
            flex-direction: column;
            gap: 15px;
            }
            .btn-info {
                width: 80%;
            }
        }

/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
        
        /* Guide Page */

        .info-section-guide {
            display: flex;
            gap: 2rem;
            align-items: center;
            margin-bottom: 3rem;
            background-color: #ffffff;
            border-radius: 25px;
        }
        .info-section-guide .text h2 {
            /* This makes a positioning "anchor" for the block */
            position: relative;
        }

        .info-section-guide .text h2::before {
            content: "";
            position: absolute;

            left: -20px;
            top: 50%;
            transform: translateY(-50%);

            width: 10px;
            height: 1.8rem;
            background-color: #34a853;
            border-radius: 10px;
        }

        .info-section-guide .text {
            flex: 1;
            text-align: justify;
            padding-left: 30px;
            padding-right: 10px;
        }
        .info-section-guide .image-container {
            flex: 1;
            background-color: #ffffff;
            border-radius: 25px;
            max-height: 700px;
            max-width: 300px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
        }

        .info-section-guide .image {
            flex: 1;
            background-color: #ffffff;
            border-radius: 25px;
            max-height: 700px;
            max-width: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            border: 2px solid #000;
        }

/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */

        /* Auth */
        /* Auth & Profile Layouts */
        .auth-container {
            background-color: #f5f5f5; /* Light gray background */
            min-height: 90vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .auth-card {
            background: white;
            width: 100%;
            max-width: 450px; /* Standard width for login/register */
            padding: 3rem;
            border-radius: 25px; /* Large rounded corners */
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            text-align: left;
        }

        .wide-card {
            max-width: 800px; /* Wider for Profile/History */
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #000;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #999;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box; /* Fix width issues */
        }

        /* Password Eye Icon */
        .password-wrapper {
            position: relative;
        }
        .eye-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: 0.6;
        }

        /* Buttons */
        .button-group {
            text-align: center;
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .btn-green {
            background-color: #00C853; /* Bright Green */
            color: white;
            padding: 0.8rem 3rem;
            border-radius: 25px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }
        .btn-green:hover { background-color: #009624; }

        .btn-outline {
            background-color: white;
            color: #333;
            padding: 0.6rem 2rem;
            border-radius: 25px;
            border: 1px solid #333;
            font-weight: bold;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-block;
        }
        .btn-outline:hover { background-color: #f9f9f9; }

        .btn-red {
            background-color: #FF0000;
            color: white;
            padding: 0.6rem 2rem;
            border-radius: 25px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .full-btn {
            width: 100%;
            max-width: 250px;
            min-width: 200px;
            text-align: center;
            display: block;
        }

        .or-divider {
            font-weight: bold;
            color: #555;
            margin: 5px 0;
        }

        /* Profile Specific */
        .profile-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }
        .profile-actions .center-buttons {
            display: flex;
            gap: 15px;
        }

        .stacked-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin-top: 2rem;
        }

        .stacked-actions-settings {
            display: flex;
            flex-direction: column;
            align-items: left;
            gap: 15px;
            margin-top: 2rem;
        }

        /* Settings Section */
        .settings-section {
            margin-top: 3rem;       /* Push it down from the form */
            padding-top: 2rem;      /* Internal spacing */
            border-top: 2px solid #eee; /* The separator line */
            text-align: left;
            align-items: left;
        }

        .settings-section h3 {
            margin-bottom: 1.5rem;
            color: #333;
            font-size: 1.2rem;
            font-weight: bold;
        }

        /* History Specific */
        .history-container {
            background-color: #f5f5f5; /* Light gray background */
            min-height: 90vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .history-card {
            background: white;
            width: 100%;
            max-width: 1000px;
            padding: 3rem;
            border-radius: 25px; /* Large rounded corners */
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            text-align: left;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #000;
        }
        .back-btn {
            font-size: 1.5rem;
            text-decoration: none;
            color: #000;
            border: 1px solid #000;
            border-radius: 50%;
            width: 35px; height: 35px;
            display: flex; align-items: center; justify-content: center;
        }

        .history-table {
            width: 100%;
            min-height: 300px;
            border-collapse: collapse;
        }
        .history-table th {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid #ccc;
            font-weight: bold;
        }
        .history-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .icon-btn {
            border: 1px solid #ccc;
            background: white;
            padding: 5px;
            border-radius: 4px;
            margin-left: 5px;
            cursor: pointer;
            text-decoration: none;
        }

/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */
/* ====================================================================================================================================================== */

        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
            background-color: #dadada;
            color: #797979;
            font-size: 0.9rem;
        }

    </style>

    <script>
    function togglePassword(fieldId) {
        var input = document.getElementById(fieldId);
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }
    </script>

</head>
<body>

    {{-- Navigation Bar --}}
    <header class="navbar">
        {{-- <img src="favicon.png" alt="logo" class="logo::before"> --}}
        <a href="{{ url('/') }}" class="logo"><img src="{{ asset('favicon.png') }}" alt="Logo">EmiTrack</a>
        <nav>
            
            <a href="{{ url('/') }}">Home</a>

            <a href="{{ url('/calculate') }}">Hitung</a>
            

            @guest

            <a href="{{ url('/register') }}">register</a>

            <a href="{{ url('/login') }}">login</a>

            @endguest



            @auth

            <a href="{{ route('history.view') }}">history</a>
            
            <a href="{{ url('/profile') }}">Profile</a>

            @endauth


        </nav>
    </header>

    {{-- Main Content Area --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer">
        &copy; {{ date('Y') }} EmiTrack. All rights reserved.
    </footer>

</body>
</html>