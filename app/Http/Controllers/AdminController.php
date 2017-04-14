<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\UtilityHelpers;
use App\Http\Requests\ManageNewsRequest;

use Auth;
use Mail;
use Validator;

use App\NewsModel;

class AdminController extends Controller
{
    use UtilityHelpers;

    public function dashboard()
    {
        if(Auth::check()) {
            if(Auth::user()->type === 'administrator') {
                return view('admin.dashboard');
            } else {
                return redirect()->route('news.index');
            }
        } else {
            return redirect()->route('home.login');
        }
    }

    public function news()
    {
        if(Auth::check()) {
            if(Auth::user()->type === 'administrator') {
                try {
                    $news = NewsModel::all();

                    return view('admin.news', [
                        'news' => $news
                    ]);
                } catch(Exception $ex) {
                    return view('errors.404');
                }
            } else {
                return redirect()->route('news.index');
            }
        } else {
            return redirect()->route('home.login');
        }
    }

    public function addNews()
    {
        if(Auth::check()) {
            if(Auth::user()->type === 'administrator') {
                return view('admin.news_add');
            } else {
                return redirect()->route('news.index');
            }
        } else {
            return redirect()->route('home.login');
        }
    }

    public function postAddNews(Request $request)
    {
        $result = Validator::make($request->all(), [
            'headline' => 'required|unique:news,headline',
            'content' => 'required'
        ]);

        if($result->fails()) {
            return redirect()->route('admin.news.add')->withErrors($result)->withInput();
        } else {
            $authAccount = Auth::user();
            $headline = trim($request->input('headline'));
            $content = trim($request->input('content'));

            $news_id = $this->insertRecord('news', [
                'headline' => $headline,
                'content' => $content,
                'username' => $authAccount->username
            ]);

            if($news_id) {
                $news = NewsModel::where('id', $news_id)->first();

                if($news) {
                    if(strlen($authAccount->middle_name) > 1) {
                        $full_name = $authAccount->first_name . ' ' . substr($authAccount->middle_name, 0, 1) . '. ' . $authAccount->last_name;
                    } else {
                        $full_name = $authAccount->first_name . ' ' . $authAccount->last_name;
                    }

                    $accounts = AccountsModel::all();

                    foreach($accounts as $account) {
                        Mail::send('emails.news', [
                            'first_name' => $account->userInfo->first_name,
                            'year' => date('Y', strtotime($news->created_at)),
                            'month' => date('m', strtotime($news->created_at)),
                            'day' => date('d', strtotime($news->created_at)),
                            'headline' => str_replace(' ', '_', $news->headline)
                        ], function($message) use ($account, $full_name) {
                            $message->to($account->email_address, $full_name)->subject('F.A.D.P. News Alert');
                        });
                    }

                    $this->setFlash('Success', 'News has been added.');

                    return redirect()->route('admin.news');
                } else {
                    $this->setFlash('Failed', 'Oops! News was not added.');

                    return redirect()->route('admin.news');
                }
            } else {
                $this->setFlash('Failed', 'Oops! Failed to add news.');

                return redirect()->route('admin.news');
            }
        }
    }

    public function editNews($id)
    {
        if(Auth::check()) {
            if(Auth::user()->type === 'administrator') {
                $news = NewsModel::where('id', $id)->first();

                if($news) {
                    return view('admin.news_edit', [
                        'id' => $news->id,
                        'headline' => $news->headline,
                        'content' => $news->content
                    ]);
                } else {
                    return redirect()->route('admin.news');
                }
            } else {
                return redirect()->route('news.index');
            }
        } else {
            return redirect()->route('home.login');
        }
    }

    public function postEditNews($id, Request $request)
    {
        $result = Validator::make($request->all(), [
            'content' => 'required'
        ]);

        if($result->fails()) {
            return redirect()->route('admin.news.edit')->withErrors($result)->withInput();
        } else {
            $content = trim($request->input('content'));

            $query = $this->updateRecord('news', $id, [
                'content' => $content
            ]);

            if($query) {
                $this->setFlash('Success', 'News has been updated.');

                return redirect()->route('admin.news');
            } else {
                $this->setFlash('Failed', 'No changes has been made.');

                return redirect()->route('admin.news');
            }
        }
    }

    public function postDeleteNews(Request $request)
    {
        $news_id = $request->input('newsID');

        $query = NewsModel::where('id', $news_id)->first();

        if($query) {
            $query = $this->deleteRecord('news', $news_id);

            if($query) {
                $this->setFlash('Success', 'News has been deleted.');

                return redirect()->route('admin.news');
            } else {
                $this->setFlash('Failed', 'Oops! News doesn\'t exist.');

                return redirect()->route('admin.news');
            }
        } else {
            $this->setFlash('Failed', 'Oops! News doesn\'t exist.');

            return redirect()->route('admin.news');
        }
    }
}
