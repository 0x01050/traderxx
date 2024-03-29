@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Home'"></page-menu>
        <section>
            @if(auth()->user()->type == 'administrator')
                <status-info></status-info>
            @endif
            @if($message = session('status'))
		        <client-edit v-bind:message="{{ json_encode(['status' => 'success', 'body' => [$message]]) }}" v-bind:parameter-list="{{ $parameterList }}" v-bind:param="{{ $param }}"></client-edit>
		    @elseif($errors->any())
		        <client-edit v-bind:message="{{ json_encode(['status' => 'failed', 'body' => $errors->all()]) }}" v-bind:old-input="{{ json_encode(old()) }}" v-bind:parameter-list="{{ $parameterList }}" v-bind:param="{{ $param }}"></client-edit>
		    @else
		        <client-edit v-bind:parameter-list="{{ $parameterList }}" v-bind:param="{{ $param }}"></client-edit>
		    @endif
        </sect ion>
    </div>
@endsection
