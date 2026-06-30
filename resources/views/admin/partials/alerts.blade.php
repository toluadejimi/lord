@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session()->has('message'))
    <div class="alert alert-success">{{ session()->get('message') }}</div>
@endif
@if (session()->has('error'))
    <div class="alert alert-danger">{{ session()->get('error') }}</div>
@endif
