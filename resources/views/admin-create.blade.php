@extends('layouts.app')

@section('title', 'Add Administrator')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Administrator'" v-bind:is-admin="'{{ auth()->user()->type == 'administrator' }}'"></page-menu>
        <section>
            @if(auth()->user()->type == 'administrator')
                <status-info></status-info>
            @endif
            @if($message = session('status'))
		        <admin-create v-bind:message="{{ json_encode(['status' => 'success', 'body' => [$message]]) }}" v-bind:parameter-list="{{ $parameterList }}"></admin-create>
		    @elseif($errors->any())
		        <admin-create v-bind:message="{{ json_encode(['status' => 'failed', 'body' => $errors->all()]) }}" v-bind:old-input="{{ json_encode(old()) }}" v-bind:parameter-list="{{ $parameterList }}"></admin-create>
		    @else
		        <admin-create v-bind:parameter-list="{{ $parameterList }}"></admin-create>
		    @endif
        </section>


    </div>
@endsection
