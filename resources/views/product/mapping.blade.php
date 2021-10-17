@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Port Mapping') }}</div>

                    <div class="card-body">

                        <form action="{{ route('product.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <table>
                                @foreach ($tableData as $fields)
                                    <tr>
                                        <td>
                                        <th>{{ $fields }}</th>
                                        </td>

                                        <td>
                                            <select name="mapping[{{ $fields }}]" id="">
                                                <option value="">Select</option>
                                                @foreach ($headerRow as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                                <input type="hidden" name="csvData" value="{{ serialize($combineData) }}">
                            </table>
                            <button type="submit" name="submit">Import</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
