@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Home'" v-bind:is-admin="'{{ auth()->user()->type == 'administrator' }}'"></page-menu>
        <section>
            <status-info></status-info>
            <clients></clients>
        </section>
    </div>
@endsection
