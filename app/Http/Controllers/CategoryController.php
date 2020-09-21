<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\CategoryCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CategoryController extends Controller
{

    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Get a list of categories",
     *     tags={"Categories"},
     *     description="Returns all categories from the system that the user has access to.",
     *     operationId="findCategories",
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Collection")
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *         @OA\MediaType(
     *             mediaType="text/json",
     *             @OA\Schema(ref="#/components/schemas/ErrorModel")
     *         )
     *     )
     * )
     *
     */
    public function index(Request $request)
    {
        $categories = Category::get();

        return (new CategoryCollection($categories))
            ->response()
            ->setStatusCode(200);
    }

}
