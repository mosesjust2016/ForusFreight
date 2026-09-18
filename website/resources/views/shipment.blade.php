@extends('layouts.app')

@section('title', 'Shipment '.$shipment->serial_no)

@section('content')
<section style="padding:4rem 0; background: rgb(0,127,127); color:white;">
    <div class="container">
        <h1 style="text-align:center; font-size:2.5rem; font-weight:800;">Shipment: {{ $shipment->serial_no }}</h1>
        <p style="text-align:center;">Status: <span style="font-weight:700;">{{ $shipment->status }}</span></p>
    </div>
</section>

<section style="padding:3rem 0; background: rgb(204,204,204);">
    <div class="container" style="max-width:800px; margin:auto;">

        <div style="background:white; border-radius:12px; padding:2rem; box-shadow:0 10px 30px rgba(0,0,0,.1);">
            <h2 style="margin-bottom:1rem;">Route</h2>
            <p><b>From:</b> {{ $shipment->from }}</p>
            <p><b>To:</b> {{ $shipment->to }}</p>
            <p><b>Service:</b> {{ $shipment->service }}</p>

            <h2 style="margin-top:2rem;">Timeline</h2>
            @if(!empty($shipment->history))
                <ul style="list-style:none; padding:0;">
                    @foreach($shipment->history as $step)
                        <li style="border-left:4px solid rgb(255,98,0); padding:1rem; margin-bottom:.7rem; background:#f8f8f8;">
                            <b>{{ $step['status'] }}</b> - {{ $step['location'] }} <br>
                            <small>{{ \Carbon\Carbon::parse($step['date'])->format('d M Y H:i') }}</small>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No updates yet.</p>
            @endif
        </div>

    </div>
</section>
@endsection
