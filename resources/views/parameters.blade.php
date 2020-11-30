@extends('layouts.app')

@section('title', 'Parameter')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Parameter'"></page-menu>
        <section>
            <status-info></status-info>   
        	<parameters></parameters>   
        </section>
    </div>
@endsection