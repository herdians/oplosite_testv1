@extends('layouts.master')

@section('title', 'Upload File')


@section('content')
    <h2>Upload File Disini</h2>

    {!! Form::open(array('url' => '/handleUpload', 'files' => true)) !!}
        {!! Form::file('file') !!}
        {!! Form::token() !!}
        {!! Form::submit('Upload') !!}

    {!! Form::close() !!}

@endsection
