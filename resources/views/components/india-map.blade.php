@props(['locations'])
@php
    $points = $locations->filter->has_map_point;
    $plant = $points->firstWhere('type', 'plant');
    // Labels that would overlap nearby pins are placed on the opposite side.
    $leftLabels = ['Mumbai', 'Ahmedabad', 'Udaipur', 'Jamshedpur'];
@endphp
<div class="india-map">
    <svg viewBox="-10 -10 620 620" role="img" aria-labelledby="indiaMapTitle">
        <title id="indiaMapTitle">Map of India showing our office and plant locations</title>
        <defs>
            <linearGradient id="mapFill" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#fbf3f4"/>
                <stop offset="1" stop-color="#f3dfe3"/>
            </linearGradient>
        </defs>
        {{-- Simplified, stylised outline of India (not to scale; for illustration only) --}}
        <path class="land" d="M4 276L14 264L40 266L62 262L48 236L42 220L32 210L50 190L78 192L98 170L108 152L128 142L130 128L132 112L144 100L128 90L118 86L110 62L92 40L112 18L138 9L156 16L180 34L200 40L228 32L246 40L238 62L218 78L210 98L220 112L216 122L232 132L250 142L260 146L242 174L262 182L280 192L306 202L322 200L340 212L360 218L384 222L402 222L400 206L402 192L416 188L418 204L436 214L456 214L474 214L482 212L482 198L490 192L510 178L532 166L548 170L564 162L574 176L588 186L578 198L560 204L544 218L536 238L532 256L524 272L508 272L506 290L504 306L492 310.4L486 296L486 276L478 268L472 292L464 278L468 268L484 260L488 252L480 246L440 246L436 244L437 230L420 224L408 218L404 230L408 246L400 258L414 264L416 280L420 292L422 308L418 318L400 318L388 318L378 330L370 346L346 358L334 368L320 384L306 398L286 418L264 424L258 434L246 442L242 460L246 482L244 498L238 512L236 538L226 544L218 560L204 572L190 588L182 584L170 572L164 552L156 524L144 500L134 482L128 460L118 442L108 418L102 394L96 370L96 350L96 334L92 320L92 312L94 306L86 304L80 318L76 326L60 334L48 332L38 322L20 304L32 298L44 298L48 290L34 294L18 292L12 284Z"/>

        @if ($plant)
            @foreach ($points as $loc)
                @continue($loc->is($plant))
                <path class="route" d="M{{ $plant->map_x }} {{ $plant->map_y }} Q {{ ($plant->map_x + $loc->map_x) / 2 }} {{ min($plant->map_y, $loc->map_y) - 40 }} {{ $loc->map_x }} {{ $loc->map_y }}"/>
            @endforeach
        @endif

        @foreach ($points as $loc)
            @php $left = in_array($loc->city, $leftLabels, true); @endphp
            <g class="pin {{ $loc->type }}" data-loc="{{ $loc->id }}" tabindex="0"
               data-bs-toggle="tooltip" data-bs-title="{{ $loc->type_label }}: {{ $loc->city }}{{ $loc->state ? ', '.$loc->state : '' }}">
                <circle class="pulse" cx="{{ $loc->map_x }}" cy="{{ $loc->map_y }}" r="8"/>
                <circle class="core" cx="{{ $loc->map_x }}" cy="{{ $loc->map_y }}" r="{{ $loc->type === 'regional' ? 6 : 8 }}"/>
            </g>
            <text class="pin-label {{ $loc->type !== 'regional' ? 'major' : '' }}"
                  x="{{ $left ? $loc->map_x - 12 : $loc->map_x + 12 }}" y="{{ $loc->map_y + 4 }}"
                  text-anchor="{{ $left ? 'end' : 'start' }}">{{ $loc->city }}</text>
        @endforeach
    </svg>
    <div class="map-legend">
        <span><i style="background:#22252a"></i>Corporate Office</span>
        <span><i style="background:#9c6f12"></i>Manufacturing Plant</span>
        <span><i style="background:#6b1424"></i>Regional Office</span>
    </div>
</div>
