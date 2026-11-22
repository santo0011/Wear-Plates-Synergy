{{-- Renders a pipe-table (first row = header). Params: $rows, $class (optional) --}}
@if (count($rows) > 1)
    @php $cols = max(array_map('count', $rows)); @endphp
    <div class="data-table-wrap {{ $class ?? '' }}">
        <table class="data-table">
            <thead>
                <tr>
                    @foreach (array_pad($rows[0], $cols, '') as $th)<th scope="col">{{ $th }}</th>@endforeach
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($rows, 1) as $row)
                    <tr>
                        @foreach (array_pad($row, $cols, '') as $i => $td)
                            @if ($i === 0)<th scope="row">{{ $td }}</th>@else<td>{{ $td }}</td>@endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
