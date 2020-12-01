@extends('layouts.app')

@section('title', 'Administrator')

@section('content')
    <div class="div-root">
        <page-menu v-bind:page="'Administrator'" v-bind:is-admin="'{{ auth()->user()->type == 'administrator' }}'"></page-menu>
        <section>
            @if(auth()->user()->type == 'administrator')
                <status-info></status-info>
            @endif
            <admins></admins>
        </section>
    </div>
@endsection
