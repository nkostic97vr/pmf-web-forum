@php ($max = (int)config('custom.pagination.max'))
@php ($step = (int)config('custom.pagination.step'))

@extends('layouts.admin')

@section('scripts')
    <script src="{{ asset('js/admin/sections-table.js') }}"></script>
@stop

@section("content")
    <div id="overlay"></div>
    <form action="{{ route("{$table}.index") }}" method="get">
        <div class="actions row">
            <div class="buttons col text-nowrap">
                <a href="{{ route("{$table}.create") }}" class="btn btn-primary">
                    {{ $table === 'categories' ? __('buttons.create_category') : __('buttons.create_forum') }}
                </a>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    @php ($all = if_query('filter', 'all'))
                    @php ($active = if_query('filter', 'active') || if_query('filter', null))
                    @php ($deleted = if_query('filter', 'deleted'))
                    <label class="btn btn-secondary {{ active_class($all) }}">
                        <input type="radio" name="filter" autocomplete="off" {{ active_class($all, 'checked') }} value="all"> {{ __('buttons.all') }}
                    </label>
                    <label class="btn btn-secondary {{ active_class($active) }}">
                        <input type="radio" name="filter" autocomplete="off" {{ active_class($active, 'checked') }} value="active"> {{ __('buttons.active') }}
                    </label>
                    <label class="btn btn-secondary {{ active_class($deleted) }}">
                        <input type="radio" name="filter" autocomplete="off" {{ active_class($deleted, 'checked') }} value="deleted"> {{ __('buttons.deleted') }}
                    </label>
                </div>
            </div>
            <div class="pagination justify-content-right">
                <select name="perPage" class="form-control">
                    <option value="0" {{ $perPage === 0 ? 'selected' : '' }}>&infin;</option>
                    @for ($i = $step; $i < $max; $i += $step)
                        <option value="{{ $i }}" {{ $perPage === $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                <select>
            </div>
        </div>
    </form>

    <table class="table table-striped sections" data-name="{{ $table }}">
        <thead>
            <tr>
                <th>
                    <a href="javascript:void(0)" class="sort-link" data-column="id">
                        ID @sort_icon('id')
                    </a>
                </th>
                <th>
                    <a href="javascript:void(0)" class="sort-link" data-column="title">
                        {{ __('db.title') }} @sort_icon('title')
                    </a>
                </th>
                <th colspan="3">&nbsp;</th>
            </tr>
        </thead>
        <tbody class="table-hover">
            @foreach ($rows as $row)
                <tr id="row-{{ $row->id }}">
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->title }}</td>
                    <td>
                        <a href="{{ route("{$table}.show", ["{$table}" => $row->id]) }}" class="btn btn-xs btn-success">
                            {{ __('buttons.view') }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route("{$table}.edit", ["{$table}" => $row->id]) }}" class="btn btn-xs btn-info">
                            {{ __('buttons.edit') }}
                        </a>
                    </td>
                    <td>
                        @if ($row->trashed())
                            <form action="{{ route("{$table}.restore", ["{$table}" => $row->id]) }}" method="post">
                                @csrf
                                <button class="btn btn-xs btn-danger" type="submit">{{ __('buttons.restore') }}</button>
                            </form>
                        @else
                            <form action="{{ route("{$table}.destroy", ["{$table}" => $row->id]) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <button class="btn btn-xs btn-danger" type="submit">{{ __('buttons.delete') }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($perPage > 0)
        <div class="row justify-content-center">
            {{ $rows->appends('perPage', $perPage)->links() }}
        </div>
    @endif
@stop