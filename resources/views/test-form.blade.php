<form action="{{route('add.customer')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Select CSV File</label><br>
    <input type="file" name="csv_file" required>
    <br><br>
    <button type="submit">Upload</button>
</form>