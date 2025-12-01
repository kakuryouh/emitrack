<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmiTrack</title>
    {{-- Basic Styling --}}
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #2d8a64;
            text-decoration: none;
            margin-right: 1rem;
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

        /* Result Page Styling */
        .container-result {
            width: 100%;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .result-box {
            background-color: #e6f9f0; /* Light green */
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem auto;
            text-align: center;
        }
        .result-box h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
            margin-top: 0;
        }
        .result-box .emission-value {
            font-size: 3rem;
            font-weight: bold;
            color: #2d8a64;
            margin: 1rem 0 2rem 0;
        }
        .result-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            text-align: center;
            gap: 1.5rem;
            max-width: 800px;
            margin: 0 auto;
        }
        .result-details div {
            font-size: 1rem;
        }
        .result-details .label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }
        .result-details .value {
            font-size: 1.1rem;
            color: #555;
        }
        /* Grid layout for the bottom 4 items */
        .result-bottom {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            text-align: center;
            margin-top: 2rem;
            border-top: 1px solid #cce8dc;
            padding-top: 2rem;
        }

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
</head>
<body>

    {{-- Navigation Bar --}}
    <header class="navbar">
        <a href="{{ url('/') }}" class="logo">EmiTrack</a>
        <nav>
            
            <a href="{{ url('/') }}">Home</a>

            <a href="{{ url('/calculate') }}">Calculate</a>

            {{-- <a href="{{ url('/guide') }}">Guide</a> --}}
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