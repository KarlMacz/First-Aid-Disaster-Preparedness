@extends('layouts.master_main')

@section('content')
    <div class="navbar navbar-inverse navbar-fixed-top shadow no-margin">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('home.index') }}">First-aid & Disaster Preparedness</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="{{ route('health_and_safety.index') }}">Health & Safety</a></li>
                <li><a href="{{ route('news.index') }}">News</a></li>
                <li><a href="{{ route('home.help') }}">Help</a></li>
                <li><a href="{{ route('home.about') }}">About Us</a></li>
            </ul>
            <ul class="nav navbar-nav pull-right">
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->userInfo->first_name . ' ' . Auth::user()->userInfo->last_name }} <span class="caret"></span></a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="{{ route('home.profile', ['username' => Auth::user()->username]) }}">Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="{{ route('home.logout') }}">Logout</a></li>
                     </ul>
                </li>
            </ul>
        </div>
    </div>
    <div id="admin-page-wrapper">
        <div class="sidebar">
            <div id="sidebar-collapse" class="sidebar-nav navbar-collapse">
                <ul class="nav">
                    <li><a href="{{ route('admin.dashboard') }}"><span class="fa fa-dashboard"></span> Dashboard</a></li>
                    <li><a href="{{ route('admin.news') }}"><span class="fa fa-newspaper-o"></span> Manage News</a></li>
                    <li class="active"><a href="{{ route('admin.health_and_safety') }}"><span class="fa fa-medkit"></span> Manage Health & Safety Tips</a></li>
                    <li><a href="{{ route('admin.users') }}"><span class="fa fa-users"></span> Manage Users</a></li>
                </ul>
            </div>
        </div>
        <div id="page-wrapper">
            <div class="visible-xs-block clearfix text-right">
                <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="fa fa-bars"></span>
                </button>
            </div>
            <div id="admin-container">
                <h3 class="no-margin"><span class="fa fa-medkit"></span> Manage Health & Safety Tips</h3>
                <hr>
                @include('partials.flash')
                <div class="form-group text-right">
                    <a href="{{ route('admin.health_and_safety.add') }}" class="btn btn-primary"><span class="fa fa-plus"></span> Add</a>
                </div>
                <table class="table table-striped table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th class="hidden-xs">Posted By</th>
                            <th>Date & Time Posted</th>
                            <th width="20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tips as $tip)
                            <tr>
                                <td>{{ $tip->id }}</td>
                                <td>{{ $tip->title }}</td>
                                <td class="hidden-xs">
                                    @if(strlen($tip->accountInfo->userinfo->middle_name) > 1)
                                        {{ $tip->accountInfo->userinfo->first_name . ' ' . substr($tip->accountInfo->userinfo->middle_name, 0, 1) . '. ' . $tip->accountInfo->userinfo->last_name }}
                                    @else
                                        {{ $tip->accountInfo->userinfo->first_name . ' ' . $tip->accountInfo->userinfo->last_name }}
                                    @endif
                                </td>
                                <td>
                                    <span class="visible-xs">{{ date('M. d, Y (h:iA)', strtotime($tip->created_at)) }}</span>
                                    <span class="hidden-xs">{{ date('F d, Y (h:iA)', strtotime($tip->created_at)) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.health_and_safety.edit', ['id' => $tip->id]) }}" class="btn btn-success btn-sm"><span class="fa fa-pencil"></span> Edit</a>
                                    <button data-button="delete-health-and-safety-button" data-var-id="{{ $tip->id }}" class="btn btn-danger btn-sm"><span class="fa fa-trash"></span> Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="delete-health-and-safety-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span class="fa fa-newspaper-o"></span> Delete News</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this health & safety tip?</p>
                    <form data-form="delete-health-and-safety-form" action="{{ route('admin.health_and_safety.delete') }}" method="POST" autocomplete="off">
                        {{ csrf_field() }}
                        <input type="hidden" name="healthAndSafetyID" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <button class="yes-button btn btn-primary">Yes</button>
                        <button class="no-button btn btn-default">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ url('/js/admin/health_and_safety.js') }}"></script>
@stop