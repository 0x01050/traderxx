@extends('layouts.app')

@section('title', 'Edit Parameter')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Parameter'" v-bind:is-admin="'{{ auth()->user()->type == 'administrator' }}'"></page-menu>
        <section>
            <status-info></status-info>
            @if($message = session('status'))
		    <parameter-edit v-bind:message="{{ json_encode(['status' => 'success', 'body' => [$message]]) }}" v-bind:param="{{ $param }}"></parameter-edit>
		    @elseif($errors->any())
		    <parameter-edit v-bind:message="{{ json_encode(['status' => 'failed', 'body' => $errors->all()]) }}" v-bind:old-input="{{ json_encode(old()) }}" v-bind:param="{{ $param }}"></parameter-edit>
		    @else
		    <parameter-edit v-bind:param="{{ $param }}"></parameter-edit>
		    @endif
        </section>
    </div>
@endsection
