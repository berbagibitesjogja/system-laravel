<style>
    table,
    th,
    td {
        border: black solid 1px;
        border-collapse: collapse;
        padding: 5px
    }
</style>
<table>
    <thead>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nama</th>
        <th>Makanan</th>
        <th>Jumlah</th>
        <th>Berat</th>
        <th>Heroes</th>
        <th>Total Food</th>
    </thead>
    <tbody>
        @foreach (App\Models\Donation\Donation::with(['foods', 'heroes', 'sponsor'])->orderBy('take')->get() as $number => $item)
            <tr>
                <td align="center">{{ $number + 1 }}</td>
                <td>
                    {{ Carbon\Carbon::createFromFormat('Y-m-d', $item->take)->isoformat('dddd, D MMMM Y') }}
                    @if ($item->notes)
                        <br>
                        *{{ $item->notes }}
                    @endif
                </td>
                <td align="center">{{ $item->sponsor->name }}</td>
                <td align="left">
                    @foreach ($item->foods as $fitem)
                        {{ $fitem->name }}
                        <br>
                    @endforeach
                </td>
                <td align="left">
                    @foreach ($item->foods as $fitem)
                        {{ $fitem->quantity }}
                        <br>
                    @endforeach
                </td>
                <td align="right">
                    @foreach ($item->foods as $fitem)
                        {{ $fitem->weight }} {{ $fitem->unit }}
                        <br>
                    @endforeach
                </td>
                <td align="center">{{ $item->heroes->sum('quantity') }}</td>
                <td align="center">{{ $item->foods->sum('weight') / 1000 }} kg</td>
            </tr>
        @endforeach
    </tbody>
</table>