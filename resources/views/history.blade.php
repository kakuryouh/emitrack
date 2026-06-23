@extends('layouts.app')

@section('content')
<div class="log-container">
    <div class="log-card wide-card">
        {{-- Header Row --}}
        <div class="log-header">
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
            <table class="log-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Transportation Mode</th>
                        <th>Total Emission</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>

                @if (empty($logs->id))
                    <tbody>
                        @foreach($logs as $index => $log)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $log->origin ? : 'none'}}</td>
                            <td>{{ $log->destination ? : 'none'}}</td>
                            <td>{{ $log->transport_type }}</td>
                            <td style="font-weight: bold;">{{ number_format($log->emissions_g) }} g</td>
                            <td>{{ $log->log_date }}</td>

                            <td class="actions">
                                {{-- View Button --}}
                                <form action="{{ route('calculate') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="origin" value="{{ $log->origin }}">
                                    <input type="hidden" name="destination" value="{{ $log->destination }}">
                                    <input type="hidden" name="vehicle_model" value="{{ $log->transportation_mode }}">
                                    <input type="hidden" name="createFlag" value= "false">

                                    <button type="submit" class="icon-btn">👁️</button>
                                </form>
                                {{-- Delete Button --}}

                                <form action="{{ route('log.delete') }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $log->id }}">
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