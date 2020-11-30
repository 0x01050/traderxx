@extends('layouts.app')

@section('title', 'Add Parameter')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Parameter'"></page-menu>
        <section>
            <status-info></status-info>   
            @if($message = session('status'))
		    <parameter-create v-bind:message="{{ json_encode(['status' => 'success', 'body' => [$message]]) }}"></parameter-create>
		    @elseif($errors->any())
		    <parameter-create v-bind:message="{{ json_encode(['status' => 'failed', 'body' => $errors->all()]) }}" v-bind:old-input="{{ json_encode(old()) }}"></parameter-create>
		    @else
		    <parameter-create></parameter-create>
		    @endif 
        </section>

        
    </div>
@endsection