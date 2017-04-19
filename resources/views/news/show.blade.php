@extends('layouts.master_main')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

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
                <li><a href="{{ route('home.donate') }}">Donate</a></li>
                <li class="active"><a href="{{ route('news.index') }}">News</a></li>
                <li><a href="{{ route('home.help') }}">Help</a></li>
                <li><a href="{{ route('home.about') }}">About Us</a></li>
            </ul>
            <ul class="nav navbar-nav pull-right">
                @if(Auth::check())
                    @if(Auth::user()->type === 'administrator')
                        <li><a href="{{ route('admin.dashboard') }}">Go to Admin Page</a></li>
                    @endif
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->userInfo->first_name . ' ' . Auth::user()->userInfo->last_name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="{{ route('home.profile') }}">Profile</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{ route('home.logout') }}">Logout</a></li>
                         </ul>
                    </li>
                @else
                    <li><a href="{{ route('home.login') }}">Login / Register</a></li>
                @endif
            </ul>
        </div>
    </div>
    <div id="main-container" class="container for-all">
        <div class="row">
            <div class="col-sm-4">
                <form data-form="search-form" action="{{ route('news.index') }}" method="POST" autocomplete="off">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="">Search For:</label>
                        <input id="search-field" type="text" class="form-control" name="search" required>
                    </div>
                </form>
            </div>
            <div class="col-sm-8">
                <div class="cards">
                    <a href="{{ route('news.index') }}" class="card">
                        <div class="card-content">
                            <span class="fa fa-arrow-left"></span> Go Back
                        </div>
                    </a>
                    <div id="news-block" class="card" data-var-id="{{ $news->id }}">
                        <div class="card-title">{{ $news->headline }}</div>
                        <div class="card-by">Posted by {{ $news->username }}</div>
                        <div class="card-content">{!! nl2br($news->content) !!}</div>
                        <div class="card-footer">
                            <ul class="tabs">
                                <li class="active"><a href="#comment" class="tab">Comment</a></li>
                                <li><a href="#share" class="tab">Share</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="comment">
                                    <div class="block" style="margin-bottom: 10px;">
                                        @if(Auth::check())
                                            @if(Auth::user()->is_banned)
                                                <div class="content">Oops! You have been banned from commenting. Please contact the administrator.</div>
                                            @else
                                                <div class="image">
                                                    <img src="/uploads/{{ (Auth::user()->image !== null ? Auth::user()->image : 'fadp_anonymous.png') }}" class="round">
                                                </div>
                                                <div class="content">
                                                    <form id="comment-form" data-form="comment-form" autocomplete="off">
                                                        <input type="hidden" name="newsID" value="{{ $news->id }}">
                                                        <div class="form-group no-margin">
                                                            <input type="text" class="form-control" name="comment" maxlength="2000" placeholder="Write a comment..." required autofocus>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        @else
                                            <div class="content">Oops! Only users who have logged in are allowed to leave a comment on this news.</div>
                                        @endif
                                    </div>
                                    <div id="comments-block" class="block-list"></div>
                                </div>
                                <div class="tab-pane" id="share">2</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="captcha-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="fa fa-hand-paper-o"></span> reCAPTCH</h4>
                </div>
                <div class="modal-body">
                    <form id="captcha-form" data-form="captcha-form">
                        {!! app('captcha')->display(); !!}
                        <div class="form-group text-right">
                            <input type="submit" class="btn btn-primary" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ url('/js/news/show.js') }}"></script>
@stop
