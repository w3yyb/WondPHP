<html>
    <body>
        <h1>Hello, @{!! $name !!}.</h1>

        @json($haha);


        @verbatim
    <div class="container">
        Hello, {{ name }}.
    </div>
@endverbatim


@if (count($haha) === 1)
    I have one record!
@elseif (count($haha) > 1)
    I have multiple haha!
@else
    I don't have any haha!
@endif


@isset($haha)
     $records is defined and is not null...
@endisset


@production
    // Production specific content...
@endproduction

@env('staging')
    // The application is running in "staging"...
@endenv


@hasSection('navigation')
    <div class="pull-right">
        @yield('navigation')
    </div>

    <div class="clearfix"></div>
@endif



@sectionMissing('navigation')
    <div class="pull-right">
       dddddddddd
    </div>
@endif

@for ($i = 0; $i < 10; $i++)
    The current value is {{ $i }}
@endfor 


@php
    $isActive = false;
    $hasError = true;
@endphp

<span @class([
    'p-4',
    'font-bold' => $isActive,
    'text-gray-500' => ! $isActive,
    'bg-red' => $hasError,
])></span>


<span class="p-4 text-gray-500 bg-red">ffffffffff</span>


@include('home', ['status' => 'complete'])
 

{{-- This comment will not be present in the rendered HTML --}}

<form method="POST" action="/profile">
    @csrf

    ...
</form>

<form action="/foo/bar" method="POST">
    @method('PUT')

    ...
</form>

@push('scripts')
    <script src="/example.js"></script>
@endpush
<head>
    <!-- Head Contents -->

    @stack('scripts')
</head>


    </body>
</html>