@extends('layouts.app')

@section('content')
<h1>{{ $product->name }}</h1>
<ul>
    <li><strong>Article:</strong> {{ $product->article }}</li>
    <li><strong>Status:</strong> {{ $product->status }}</li>
    @if($product->data)
        <li><strong>Data:</strong>
        <ul>
            @foreach($product->data as $key => $value)
                <li>{{ $key }}: {{ $value }}</li>
            @endforeach
        </ul>
    @endif
</ul>
<a href="{{ route('products.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
