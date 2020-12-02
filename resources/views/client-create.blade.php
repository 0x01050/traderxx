@extends('layouts.app')

@section('title', 'Add User')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Home'" v-bind:is-admin="'{{ auth()->user()->type == 'administrator' }}'"></page-menu>
        <section>
            @if(auth()->user()->type == 'administrator')
                <status-info></status-info>
            @endif
            @if($message = session('status'))
		        <client-create v-bind:message="{{ json_encode(['status' => 'success', 'body' => [$message]]) }}" v-bind:parameter-list="{{ $parameterList }}"></client-create>
		    @elseif($errors->any())
		        <client-create v-bind:message="{{ json_encode(['status' => 'failed', 'body' => $errors->all()]) }}" v-bind:old-input="{{ json_encode(old()) }}" v-bind:parameter-list="{{ $parameterList }}"></client-create>
		    @else
		        <client-create v-bind:parameter-list="{{ $parameterList }}"></client-create>
		    @endif
        </section>


    </div>
@endsection
