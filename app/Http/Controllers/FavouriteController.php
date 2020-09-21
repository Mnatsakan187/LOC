<?php

namespace App\Http\Controllers;

use App\FollowUser;
use App\Http\Resources\ProfileCollection;
use App\Profile;
use App\Project;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectCollection;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFavouriteProjects(Request $request)
    {
        $projects = Project::whereHas('likes', function($q)             {
                $q->where('likes.user_id', '=', Auth::user()->id);
        })->orderBy('id', 'desc');

        // Search for name
        if ($request->has('search')) {
            $projects =  $projects->where('name', 'like', '%'. $request->input('search'). '%');
        }



        $projects =  $projects->paginate(10);

        return (new ProjectCollection($projects))
            ->response()
            ->setStatusCode(200);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFavouriteProfiles(Request $request)
    {
        $profiles = Profile::whereHas('likes', function($q)             {
            $q->where('likes.user_id', '=', Auth::user()->id);
        })->orderBy('id', 'desc');

        // Search for name
        if ($request->has('search')) {
            $profiles =  $profiles->where('name', 'like', '%'. $request->input('search'). '%');
        }

        if($request->has('type') && $request->type != 'undefined') {
            $profiles =  $profiles->where('type',  $request->input('type'));
        }

        $profiles =  $profiles->paginate(10);

        return (new ProfileCollection($profiles))
            ->response()
            ->setStatusCode(200);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFollowedCreators()
    {
        $ids = FollowUser::where('user_id', Auth::user()->id)->pluck('follow_by_user_id')->toArray();
        $profiles = Profile::orderBy('id', 'desc')->whereIn('id',  $ids)->get();

        return (new ProfileCollection($profiles))
            ->response()
            ->setStatusCode(200);

    }
}
