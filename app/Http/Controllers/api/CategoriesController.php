<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class CategoriesController extends Controller
{
    /**
     * @OA\Get(
     *     tags={"Category"},
     *     path="/api/categories",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number to retrieve",
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter categories",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="List Categories.")
     * )
     */
    public function index(Request $request) : JsonResponse
    {
        $perPage = 4;
        $page = $request->query('page', 1);
        $searchTerm = $request->query('search');

        $query = Categories::query();

        $query->where("is_delete", false);
        if ($searchTerm) {
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        $data = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json($data)
            ->header("Content-Type", 'application/json; charset=utf-8');
    }
//    /**
//     * @OA\Get(
//     *     tags={"Category"},
//     *     path="/api/categories",
//     *     @OA\Response(response="200", description="List Categories.")
//     * )
//     */
//    public function index(): JsonResponse
//    {
//        $data = Categories::all();
//        return response()->json($data)
//            ->header("Content-Type", 'application/json; charset=utf-8');
//    }
    /**
     * @OA\Get(
     *     tags={"Category"},
     *     path="/api/categories/{id}",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ідентифікатор категорії",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(response="200", description="List Categories."),
     * @OA\Response(
     *    response=404,
     *    description="Wrong id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong Category Id has been sent. Pls try another one.")
     *        )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $category = Categories::findOrFail($id);
        return response()->json($category,200, ['Charset' => 'utf-8']);
    }
    /**
     * @OA\Post(
     *     tags={"Category"},
     *     path="/api/categories/store",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","image"},
     *                 @OA\Property(
     *                      property="image",
     *                      type="file",
     *                  ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Add Category.")
     * )
     */
    public function store(Request $request) : JsonResponse
    {
        $folder = public_path("uploads");
        if (!file_exists($folder)){
            mkdir($folder);
        }

        $filename = uniqid().'.'.'webp';
        $manager = new ImageManager(new Driver());
        $sizes = [100, 300, 600, 1200];

        foreach ($sizes as $size){
            $image = $manager->read($request->file('image'));
            $image->scale($size);
            $image->toWebp()->save(public_path('uploads/'.$size.'_'.$filename));
        }

        $category = Categories::create([
            'name' => $request->name,
            'image' => $filename
        ]);

        return response()->json($category, 201)
            ->header("Content-Type", 'application/json; charset=utf-8');
    }
    /**
     * @OA\Post(
     *     tags={"Category"},
     *     path="/api/categories/update/{id}",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Ідентифікатор категорії",
     *          required=true,
     *          @OA\Schema(
     *              type="number",
     *              format="int64"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "image"},
     *                  @OA\Property(
     *                       property="image",
     *                       type="file",
     *                   ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Add Category.")
     * )
     */
    public function update($id, Request $request) : JsonResponse {
        $folder = public_path("uploads");
        if (!file_exists($folder)){
            mkdir($folder);
        }

        $filename = uniqid().'.'.'webp';
        $manager = new ImageManager(new Driver());
        $sizes = [100, 300, 600, 1200];
        $category = Categories::findOrFail($id);

        foreach ($sizes as $size){
            if(file_exists(public_path('uploads/'.$size.'_'.$category->image))) {
                File::delete(public_path('uploads/'.$size.'_'.$category->image));
            }

            $image = $manager->read($request->file('image'));
            $image->scale($size);
            $image->toWebp()->save(public_path('uploads/'.$size.'_'.$filename));
        }


        $category->update([
            'name' => $request->name,
            'image' => $filename
        ]);
        return response()->json($category,200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category id",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="not auth"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $category = Categories::find($id);
        $category->update(["is_delete"=>true]);

//        if (!$category) {
//            return response()->json(["error" => "Категорії не знайдено"], 404);
//        }
//        $sizes = [100, 300, 600, 1200];
//
//        foreach ($sizes as $size){
//            if(file_exists(public_path('uploads/'.$size.'_'.$category->image))) {
//                File::delete(public_path('uploads/'.$size.'_'.$category->image));
//            }
//        }
//        $category->delete();

        return response()->json("", 200, ['Charset' => 'utf-8']);
    }
}
