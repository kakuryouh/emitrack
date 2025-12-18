@extends('layouts.app')

@section('content')
<div class="history-container">
    <div class="history-card wide-card">
        {{-- Header Row --}}
        <div class="history-header">
            <a href="{{ url('/') }}" class="back-btn">←</a>
            
            <div class="sort-box">
                <label>Sort by:</label>
                <select onchange="window.location.href='?sort='+this.value">
                    <option value="newest">Newest</option>
                    <option value="oldest">Oldest</option>
                    <option value="highest">Highest Emission</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Transportation Mode</th>
                        <th>Total Emission</th>
                        <th>Date</th>
                        <th></th> {{-- Actions --}}
                    </tr>
                </thead>

                @if (empty($histories->id))
                    <tbody>
                        {{-- Loop through data passed from Controller --}}
                        @foreach($histories as $index => $history)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $history->origin }}</td>
                            <td>{{ $history->destination }}</td>
                            <td>{{ $history->transportation_mode }}</td>
                            <td style="font-weight: bold;">{{ number_format($history->total_emission) }} g</td>
                            <td>{{ date('d M Y', strtotime($history->created_at)) }}</td>

                            <td class="actions">
                                {{-- View Button --}}
                                <form action="{{ route('calculate') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="origin" value="{{ $history->origin }}">
                                    <input type="hidden" name="destination" value="{{ $history->destination }}">
                                    <input type="hidden" name="vehicle_model" value="{{ $history->transportation_mode }}">
                                    <input type="hidden" name="createFlag" value= "false">

                                    <button type="submit" class="icon-btn">👁️</button>
                                </form>
                                {{-- Delete Button --}}

                                <form action="{{ route('history.delete') }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $history->id }}">
                                    <button type="submit" class="icon-btn delete-btn">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    
                    </tbody>
                
                @else

                    <tbody>
                        <tr>
                            <td colspan="7" style="text-align: center;">No data</td>
                        </tr>                        
                    </tbody>

                @endif

            </table>
        </div>

    </div>
</div>
@endsection