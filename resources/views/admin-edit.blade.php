@extends('layouts.app')

@section('title', 'Edit Administrator')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Administrator'" v-bind:is-admin="'{{ auth()->user()->type == 'administrator' }}'"></page-menu>
        <section>
            @if(auth()->user()->type == 'administrator')
                <status-info></status-info>
            @endif
            @if($message = session('status'))
		        <admin-edit v-bind:message="{{ json_encode(['status' => 'success', 'body' => [$message]]) }}" v-bind:parameter-list="{{ $parameterList }}" v-bind:admin="{{ $param }}"></admin-edit>
		    @elseif($errors->any())
		        <admin-edit v-bind:message="{{ json_encode(['status' => 'failed', 'body' => $errors->all()]) }}" v-bind:old-input="{{ json_encode(old()) }}" v-bind:parameter-list="{{ $parameterList }}" v-bind:admin="{{ $param }}"></admin-edit>
		    @else
		        <admin-edit v-bind:parameter-list="{{ $parameterList }}" v-bind:admin="{{ $param }}"></admin-edit>
		    @endif
        </section>
    </div>
@endsection
