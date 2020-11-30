@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    @if($errors->any())  
    <login v-bind:message="{{ json_encode(['status' => 'failed', 'body' => $errors->all()]) }}" v-bind:param="{{ json_encode(['email' => old('email')]) }}"></login>
    @else
    <login v-bind:param="{{ json_encode(['email' => '']) }}"></login>
    @endif
@endsection
