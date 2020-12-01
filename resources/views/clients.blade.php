@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Home'" v-bind:is-admin="'{{ auth()->user()->type == 'administrator' ? true : false }}'"></page-menu>
        <section>
            @if(auth()->user()->type == 'administrator')
                <status-info></status-info>
            @endif
            <clients></clients>
        </section>
    </div>
@endsection
