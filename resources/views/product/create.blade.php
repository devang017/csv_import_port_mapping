@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Select CSV') }}</div>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('product.mapping') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <table>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group row">
                                            <label for="region_name" class="col-sm-4 col-form-label">choose csv file<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="file" name="csvFile" id=""
                                                    class="@error('csvFile') is-invalid @enderror" id="csvFile">
                                                <button type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </table>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
