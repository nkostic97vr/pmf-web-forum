@php ($max = (int)config('custom.pagination.max'))
@php ($step = (int)config('custom.pagination.step'))

@extends('layouts.admin')

@section('scripts')
    <script src="{{ asset('js/admin/sections-table.js') }}"></script>
@stop

@section("content")
    <form id="index" action="{{ route("{$table}.index") }}" method="get"></form>

    <div class="admin-actions">

        <div class="create-paginate">
            <a href="{{ route("{$table}.create") }}" class="btn btn-primary">
                {{ $table === 'categories' ? __('admin.create-category') : __('admin.create-forum') }}
            </a>
            <select name="perPage" class="form-control">
                <option value="0" {{ !$perPage ? 'selected' : '' }}>&infin;</option>
                @for ($i = $step; $i <= $max; $i += $step)
                    <option value="{{ $i }}" {{ $perPage === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            <select>
        </div>

        <div class="filter-search">
            <div class="search text-nowrap">
                <input type="text" name="search_query" class="form-control">
                <button type="submit" class="btn btn-info">{{ __('admin.search') }}</button>
            </div>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary {{ active_class($filter === 'all') }}">
                    <input type="radio" name="filter" autocomplete="off" {{ active_class($filter === 'all', 'checked') }} value="all"> {{ __('admin.all') }}
                </label>
                <label class="btn btn-secondary {{ active_class($filter === 'active') }}">
                    <input type="radio" name="filter" autocomplete="off" {{ active_class($filter === 'active', 'checked') }} value="active"> {{ __('admin.active') }}
                </label>
                <label class="btn btn-secondary {{ active_class($filter === 'trashed') }}">
                    <input type="radio" name="filter" autocomplete="off" {{ active_class($filter === 'trashed', 'checked') }} value="trashed"> {{ __('admin.trashed') }}
                </label>
            </div>
        </div>

    </div>

    <div class="table-responsive">
        <table class="table table-striped sections" data-name="{{ $table }}">
            <thead class="text-nowrap">
                <tr>
                    @sections_th(id)
                    @sections_th(title)
                    @if ($table === 'forums')
                        @sections_th(category)
                    @endif
                    <th colspan="3">&nbsp;</th>
                </tr>
            </thead>
            <tbody class="table-hover">
                @foreach ($rows as $row)
                    <tr id="row-{{ $row->id }}">
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->title }}</td>

                        @if ($table === 'forums')
                            <td>{{ $row->category_title }}</td>
                        @endif

                        <td>
                            <a href="{{ route("{$table}.show", ["{$table}" => $row->id]) }}" class="btn btn-xs btn-success">
                                {{ __('admin.view') }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route("{$table}.edit", ["{$table}" => $row->id]) }}" class="btn btn-xs btn-info">
                                {{ __('admin.edit') }}
                            </a>
                        </td>
                        <td>
                            @if ($row->trashed())
                                <form action="{{ route("{$table}.restore", ["{$table}" => $row->id]) }}" method="post">
                                    @csrf
                                    <button class="btn btn-xs btn-danger" type="submit">{{ __('admin.restore') }}</button>
                                </form>
                            @else
                                <form action="{{ route("{$table}.destroy", ["{$table}" => $row->id]) }}" method="post">
                                    @csrf
                                    {{ method_field('DELETE') }}
                                    <button class="btn btn-xs btn-danger" type="submit">{{ __('admin.delete') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($perPage > 0)
        <div class="row justify-content-center">
            {{ $rows->appends('perPage', $perPage)->appends('filter', $filter)->links() }}
        </div>
    @endif
@stop
