<?php

namespace App\Http\Controllers;

use App\Collection;
use App\Event;
use App\FollowUser;
use App\Hide;
use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Profile;
use App\Project;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectCollection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class HudController extends Controller
{
    /**
     * Display a listing of the resource.
     *0
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $hided = $this->getHidedProjectsProfilesEventsFor($user);

        $projects = Project::query()
            ->doesnthave("collections")
            ->orderBy('id', 'desc')
            ->whereNotIn('id', $hided['projects'])
            ->where('updated', '=', 1)
            ->where('updated_color', '=', 1);

        $events = Event::query()
            ->doesnthave("collections")
            ->orderBy('id', 'desc')
            ->whereNotIn('id', $hided['events'])
            ->where('updated', '=', 1)
            ->where('updated_color', '=', 1);

        $collections = Collection::orderBy('id', 'desc')
            ->where('user_id', $user->getKey())
            ->where('hide', '!=', 1)
            ->where('updated', '=', 1);

        $profileIds = FollowUser::where('user_id', '=', $user->getKey())->pluck('follow_by_user_id')->toArray();

        $profiles = Profile::query()
            ->doesnthave("collections")
            ->orderBy('profiles.id', 'desc')
            ->whereIn('profiles.id',  $profileIds)
            ->select('profiles.id as id', 'profiles.user_id as user_id', 'profiles.creative_title as creative_title', 'profiles.updated')
            ->leftJoin('users', 'users.id', '=', 'profiles.user_id')
            ->where('profiles.user_id', '!=', $user->getKey())
            ->whereNotIn('profiles.id', $hided['profiles'])
            ->where('profiles.updated', '=', 1)
            ->where('profiles.updated_color', '=', 1);


        if ($request->has('search')) {
            $projects = $projects->where('name', 'like', '%' . $request->input('search') . '%');
            $events = $events->where('name', 'like', '%' . $request->input('search') . '%');
            $collections = $collections->where('name', 'like', '%' . $request->input('search') . '%');
            $profiles = $profiles->where('creative_title', 'like', '%' . $request->input('search') . '%');
        }

        if($request->type == 1){
            $projects = $projects->where('type', '=',  0);
        }elseif ($request->type == 2){
            $projects = $projects->where('type', '=',  1);
        }elseif ($request->type == 3){
            $projects = $projects->where('type', '=',  2);
        }elseif ($request->type == 4){
            $projects = $projects->where('type', '=',  3);
        }

        if(!$request->type){
            $projects = $projects->get();
            $events = $events->get();
            $collections = $collections->get();
            $profiles = $profiles->get();

            $hud = collect();

            foreach ($projects as $project) {
                $hud->push(new ProjectResource($project));
            }

            foreach ($events as $event) {
                $hud->push(new EventResource($event));
            }

            foreach ($profiles as $profile) {
                $hud->push(new ProfileResource($profile));
            }

            foreach ($collections as $collection) {
                $hud->push(new CollectionResource($collection));
            }

            $hudSort = $hud->sortByDesc('updated_at');
        }else{
            $projects = $projects->get();
            $hud = collect();
            foreach ($projects as $project) {
                $hud->push(new ProjectResource($project));
            }
            $hudSort = $hud->sortByDesc('updated_at');
        }

        $sortedHud = collect();
        foreach ($hudSort as $item) {
            $sortedHud->push($item);
        }

        $page = $request->page;
        $perPage = $request->perPage;

        $paginate = new LengthAwarePaginator($sortedHud->forPage($page, $perPage), $sortedHud->count(), $perPage, $page);

        return response($paginate)
            ->setStatusCode(200);
    }


    public function discover(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $hided = $this->getHidedProjectsProfilesEventsFor($user);

        $projects = Project::query()
            ->doesnthave("collections")
            ->whereNotIn('id', $hided['projects'])
            ->orderBy('id', 'desc');

        $profiles = Profile::query()
            ->doesnthave("collections")
            ->orderBy('profiles.id', 'desc')
            ->select('profiles.id as id', 'profiles.user_id as user_id', 'profiles.creative_title as creative_title')
            ->leftJoin('users', 'users.id', '=', 'profiles.user_id')
            ->where('profiles.user_id', '!=', $user->getKey())
            ->whereNotIn('profiles.id', $hided['profiles']);

        if($request->type == 1){
            $projects = $projects->where('type', '=',  0);
        }elseif ($request->type == 2){
            $projects = $projects->where('type', '=',  1);
        }elseif ($request->type == 3){
            $projects = $projects->where('type', '=',  2);
        }elseif ($request->type == 4){
            $projects = $projects->where('type', '=',  3);
        }

        $hud = collect();
        if(!$request->type){
            $projects = $projects->get();

            $profiles = $profiles->get();

            foreach ($projects as $project) {
                $hud->push(new ProjectResource($project));
            }

            foreach ($profiles as $profile) {
                $hud->push(new ProfileResource($profile));
            }

        }else{
            $projects = $projects->get();

            foreach ($projects as $project) {
                $hud->push(new ProjectResource($project));
            }
        }

        $hudSort = $hud->sortByDesc('created_at');

        $sortedHud = collect();
        foreach ($hudSort as $item) {
            $sortedHud->push($item);
        }

        $page = $request->page;
        $perPage = $request->perPage;
        $paginate = new LengthAwarePaginator($sortedHud->forPage($page, $perPage), $sortedHud->count(), $perPage, $page);
        return response($paginate)
            ->setStatusCode(200);
    }

    private function getHidedProjectsProfilesEventsFor(User $user)
    {
        $hidedProjects = $hidedProfiles = $hidedEvents = [];

        $user->hides->map(function ($item, $key) use (&$hidedProjects, &$hidedProfiles, &$hidedEvents) {
            /** @var Hide $item */

            if ($item->hideable) {
                $id = $item->hideable->getKey();

                if ($item->hideable instanceof Project) $hidedProjects[] = $id;
                if ($item->hideable instanceof Profile) $hidedProfiles[] = $id;
                if ($item->hideable instanceof Event) $hidedEvents[] = $id;
            }
        });

        return [
            'projects' => $hidedProjects,
            'profiles' => $hidedProfiles,
            'events' => $hidedEvents
        ];
    }


    public function searchHud(Request $request)
    {
        $hud = collect();
        if($request->type == 1){
            $creators = Profile::join('users', 'users.id', '=', 'profiles.user_id');
            if ($request->has('search')) {
                $creators = $creators->where('creative_title', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('users.first_name', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('users.last_name', 'like', '%' . $request->input('search') . '%');
            }

            $ids = FollowUser::where('user_id', Auth::user()->id)->pluck('follow_by_user_id')->toArray();


            $creators = $creators->get();
            foreach ($creators as $creator) {
                foreach ($ids as $id ){
                    if ($creator->id == $id){
                        $hud->push(new ProfileResource($creator));
                    }
                }

            }
        }elseif ($request->type == 2){
            $projects = Project::orderBy('id', 'desc');

            if ($request->has('search')) {
                $projects = $projects->where('name', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('description', 'like', '%' . $request->input('search') . '%');
            }

            $projects = $projects->get();
            foreach ($projects as $project) {
                if($project->likes()->where('likes.user_id', '=', Auth::user()->id)->count()){
                    $hud->push(new ProjectResource($project));
                }
            }

        }elseif($request->type == 'all' || $request->type == ''){
            $creators = Profile::join('users', 'users.id', '=', 'profiles.user_id');
            if ($request->has('search')) {
                $creators = $creators->orwhere('profiles.creative_title', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('users.first_name', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('users.last_name', 'like', '%' . $request->input('search') . '%');
            }
            $creators =  $creators->get();


            $ids = FollowUser::where('user_id', Auth::user()->id)->pluck('follow_by_user_id')->toArray();

            foreach ($creators as $creator) {
                foreach ($ids as $id ){
                    if ($creator->id == $id){
                        $hud->push(new ProfileResource($creator));
                    }
                }
            }


            $projects = Project::orderBy('id', 'desc');

            if ($request->has('search') ) {
                $projects = $projects->where('name', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('description', 'like', '%' . $request->input('search') . '%');
            }

            $projects = $projects->get();

            foreach ($projects as $project) {
                if($project->likes()->where('likes.user_id', '=', Auth::user()->id)->count()){
                    $hud->push(new ProjectResource($project));
                }

            }
        }

        $page = $request->page;
        $perPage = $request->perPage;
        $paginate = new LengthAwarePaginator($hud->forPage($page, $perPage), $hud->count(), $perPage, $page);
        return response($paginate)
            ->setStatusCode(200);
    }



    public function getLoc(Request $request)
    {
        $hud = collect();
        if($request->type == 1){
            $creators = Profile::select('profiles.id', 'profiles.creative_title',
                'profiles.biography', 'profiles.location',
                'profiles.created_at', 'profiles.updated_at',
                'profiles.deleted_at', 'profiles.avatar_uri', 'profiles.background_uri',
                'profiles.updated', 'profiles.hide', 'profiles.updated_color')
                ->join('users', 'users.id', '=', 'profiles.user_id');
            if ($request->has('search')) {
                $creators = $creators->where('creative_title', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('users.first_name', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('users.last_name', 'like', '%' . $request->input('search') . '%');
            }

            $creators = $creators->get();
            foreach ($creators as $creator) {
                $hud->push(new ProfileResource($creator));
            }

        }elseif ($request->type == 2){
            $projects = Project::orderBy('id', 'desc');

            if ($request->has('search')) {
                $projects = $projects->where('name', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('description', 'like', '%' . $request->input('search') . '%');
            }

            $projects = $projects->get();
            foreach ($projects as $project) {
                $hud->push(new ProjectResource($project));
            }

        }elseif($request->type == 'all' || $request->type == ''){

            $creators = Profile::select('profiles.id', 'profiles.creative_title',
                'profiles.biography', 'profiles.location',
                'profiles.created_at', 'profiles.updated_at',
                'profiles.deleted_at', 'profiles.avatar_uri', 'profiles.background_uri',
                'profiles.updated', 'profiles.hide', 'profiles.updated_color')->join('users', 'users.id', '=', 'profiles.user_id');
            if ($request->has('search')) {
                $creators = $creators->where('creative_title', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('users.first_name', 'like', '%' . $request->input('search') . '%')
                    ->orwhere('users.last_name', 'like', '%' . $request->input('search') . '%');
            }
            $creators = $creators->get();
            foreach ($creators as $creators) {
                $hud->push(new ProfileResource($creators));
            }

            $projects = Project::orderBy('id', 'desc');

            if ($request->has('search')) {
                $projects = $projects->where('name', 'like', '%' . $request->input('search') . '%');
            }
            $projects = $projects->get();
            foreach ($projects as $project) {
                $hud->push(new ProjectResource($project));
            }
        }

        $hudSort = $hud->sortByDesc('updated');

        $sortedHud = collect();
        foreach ($hudSort as $item) {
            $sortedHud->push($item);
        }

        $page = $request->page;
        $perPage = $request->perPage;
        $paginate = new LengthAwarePaginator($sortedHud->forPage($page, $perPage), $sortedHud->count(), $perPage, $page);
        return response($paginate)
            ->setStatusCode(200);
    }
}
