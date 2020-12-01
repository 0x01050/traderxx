@extends('layouts.app')

@section('title', 'Parameter')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Parameter'" v-bind:is-admin="'{{ auth()->user()->type == 'administrator' }}'"></page-menu>
        <section>
        	<parameters v-bind:is-admin="'{{ auth()->user()->type == 'administrator' }}'"></parameters>
        </section>
    </div>
@endsection
