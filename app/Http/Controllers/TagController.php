<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagCollection;

use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Event;

class TagController extends Controller
{

    public function index(Request $request)
    {
        $tags = Tag::where('user_id', Auth::user()->id)->paginate(10);
        // Search for summary text
        if ($request->has('search')) {
            $tags->where('name', $request->input('search'));
        }

        return (new TagCollection($tags))
            ->response()
            ->setStatusCode(200);
    }


    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "name" => 'required|string|max:255|unique:events,name,user_id'.  Auth::user()->id,
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'name.required' => 'This name field is required',
        );
        return $validationMessages;
    }
}
