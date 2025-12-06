@extends('layouts.app')

@section('content')
    {{-- 1. Banner Section --}}
    <div class="banner">
        <h1>EMITRACK</h1>
        <h1>Calculate Your Carbon Footprint</h1>
        <p style="font-size: 1.1rem; margin-bottom: 2rem;">Understand the environmental impact of your travel.</p>
        <a href="{{ url('/calculate') }}" class="btn">Hitung Sekarang</a>
    </div>

    <div class="container-info-button">
        <a href="{{ url('/guide') }}" class="btn-info">Cara Live Tracking TransJakarta</a>
        <a href="https://commuterline.id/perjalanan-krl/jadwal-kereta" class="btn-info">Jadwal Kereta KAI</a>
    </div>

    <div class="container">
        {{-- 2. Info Section 1 --}}
        <section class="info-section">
            <div class="text">
                <h2>Apa itu Emisi Karbon?</h2>
                <p>
                    Emisi karbon adalah proses pelepasan gas karbon dioksida (CO²) ke atmosfer, baik secara alami maupun akibat aktivitas manusia.
                    Pelepasan CO² ini terjadi saat bahan bakar fosil dibakar, seperti dalam proses industri, transportasi, dan pembangkit listrik.
                </p>
            </div>
                <div class="image-container">
                    <img class="image" src="images\Traffic-1.jpg" alt="traffic-1">
            </div>
        </section>

        {{-- 3. Info Section 2 --}}
        <section class="info-section">
            <div class="text">
                <h2>Dampak Dari Emisi Karbon</h2>
                <p>
                    <h3>Emisi karbon memiliki dampak signifikan terhadap:</h3>

                    <ul>
                        <li>lingkungan</li>
                        <li>kesehatan</li>
                        <li>ekonomi</li>
                    </ul>
                    
                    <h3>Contoh Dari Dampak Emisi Karbon:</h3>

                    <ul>
                        <li>perubahan iklim</li>
                        <li>pencemaran udara dan air</li>
                        <li>hilangnya keanekaragaman hayati</li>
                        <li>masalah pernapasan</li>
                        <li>penurunan produktivitas pertanian</li>
                        <li>peningkatan biaya kesehatan.</li>
                    </ul>
                </p>
            </div>
                <div class="image-container">
                    <img class="image" src="images\Traffic-2.jpg" alt="traffic-2">
            </div>
        </section>
    </div>
@endsection