<table>
    <tbody>
        <!-- SHEET HEADER -->
        <tr>
            <td><strong>{{ $title }}</strong></td>
        </tr>

        <!-- EMPTY ROW -->
        <tr><td></td></tr>

        <tr>
            <td>Tahun Ajaran: {{ $academic_year }}</td>
        </tr>
        <tr>
            <td>Periode: {{ $period }}</td>
        </tr>
        <tr>
            <td>Jalur: {{ $path }}</td>
        </tr>
        <tr>
            <td>Program Studi: {{ $studyprogram }}</td>
        </tr>
        <tr>
            <td>Jenis Perkuliahan: {{ $lecture_type }}</td>
        </tr>
        <tr>
            <td>Jangan ubah kode berikut: {{ $encrypted_string }}</td>
        </tr>

        <!-- EMPTY ROW -->
        <tr><td></td></tr>

        <tr>
            <td>Petunjuk Pengisian Data:</td>
        </tr>
        @foreach($guides as $guide)
            <tr>
                <td>{{ $guide }}</td>
            </tr>
        @endforeach

        <!-- EMPTY ROW -->
        <tr><td></td></tr>

        <!-- TABLE HEAD -->
        <tr>
            @foreach($data[0] as $key => $value)
                <td><strong>{{ $key }}</strong></td>
            @endforeach
        </tr>

        <!-- TABLE BODY -->
        @foreach($data as $row)
            <tr>
                @foreach ($row as $item)
                    <td>{{ $item }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
