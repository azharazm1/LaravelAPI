<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;


class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest('publish_date')->get();

        /** Jika data kosong tampilkan hasil dan pesan dibawah */
        if ($articles->isEmpty()) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Article empty'
            ], Response::HTTP_NOT_FOUND);
        } else {
            /** Jika data ada tampilkan data sesuai hasil return dari fungsi map */
            return response()->json([
                'data' => $articles->map(function ($article) {
                    return [
                        'title' => $article->title,
                        'content' => $article->content,
                        'publish_date' => $article->publish_date
                    ];
                }),
                'message' => 'List articles',
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'publish_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            Article::create([
                'title' => $request->title,
                'content' => $request->content,
                'publish_date' => Carbon::create($request->publish_date)->toDateString(),
            ]);
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data stored to DB'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error storing data : ' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error storing data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $article = Article::where('id', $id)->first();
        if ($article) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'data' => [
                    'title' => $article->title,
                    'content' => $article->content,
                    'publish_date' => $article->publish_date
                ]
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Article not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Article not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'publish_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $article->update([
                'title' => $request->title,
                'content' => $request->content,
                'publish_date' => Carbon::create($request->publish_date)->toDateString(),
            ]);
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data updated'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error update data : ' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error update data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
