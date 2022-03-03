<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Like;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $like_posts = Like::where('user_id',auth()->user()->id)->get();
        // foreach($like_posts as $like_post ){
        //     $postid[]=$like_post->post_id;
        // }
        // $bookmark = Post::orderBy('created_at', 'desc')->with('like', function($like){
        //     return $like->where('user_id',auth()->user()->id)->get();
        // })->get();

        // $bookmark = Like::where('user_id',auth()->user()->id)->get();
        // //dd($bookmark);
        // $posts_ByUser = Post::whereIn('id',$postid)->get();
         //return response(['postsByUser' => $bookmark]);

        if(trim($request->search) == ""){
            return response([
                'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comment', 'like')
                ->with('like',function($like){
                    return $like->where('user_id',auth()->user()->id)->select('id','user_id','post_id')->get();
                })
                ->get()
            ], 200);
        }

        $search =   $request->search;

        return response([
            'posts' => Post::orderBy('created_at', 'desc')->where('body','like',"%{$search}%")->with('user:id,name,image')->withCount('comment', 'like')
            ->with('like',function($like){
                return $like->where('user_id',auth()->user()->id)->select('id','user_id','post_id')->get();
            })
            ->get()
        ], 200);

        // return response([
        //     'posts' => Post::orderBy('created_at', 'desc')->where('body','like',"%{$search}%")->with('user:id,name,image')->withCount('comment', 'like')
        //     ->with('like',function($like){
        //         return $like->where('user_id',auth()->user()->id)->select('id','user_id','post_id')->get();
        //     })
        //     ->get()
        // ], 200);
        //  return response([
        //     'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comment', 'like')
        //     ->with('like',function($like){
        //         return $like->where('user_id',auth()->user()->id)->select('id','user_id','post_id')->get();
        //     })
        //     ->get()
        // ], 200);

    }
    public function bookmark(){
        return response([
            'posts' => Post::orderBy('created_at','desc')->with('user:id,name,image')->withCount('comment', 'like')
            ->with('like',function($like){
                return $like->where('user_id',auth()->user()->id)->select('id','user_id','post_id')->get();
            })->wherehas('like',function($q){
                return $q->where('user_id',auth()->user()->id);
            })->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 's3');

        //dd($image);
        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
        ]);
        $post = Post::where('id',$post->id)->first();
        $post->image = $image;
        $post->save();
        return response([
            'message'=>'Post created',
            'post'=>$post
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id )
    {
        return response([
            'post'=>Post::where('id',$id)->withCount('comment','like')->get()
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message'=>'post not found.'
            ], 403);
        }
        if($post->user_id != auth()->user()->id)
        {
            return response([
                'message'=>'permission deny'
            ],403);
        }
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);
        if($request->image){



            if($post->image){
                //delete image from public/posts
                //$pathinfo = pathinfo($post->image);
                //Storage::disk('s3')->delete('/posts/'.$pathinfo['filename'].'.'.$pathinfo['extension']);
            }

            $image = $this->saveImage($request->image, 's3');
            $post->update([
                'body'=>$attrs['body'],

            ]);
            $post = Post::where('id',$post->id)->first();
            $post->image = $image;
            $post->save();
            return response([
                'meesage'=>'Post updated',
                'post'=>$post
            ],200);

        }
        $post->update([
            'body'=>$attrs['body']
        ]);

        return response([
            'meesage'=>'Post updated',
            'post'=>$post
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        $post = Post::find($id);
        if($post->image){
            //delete image from public/posts
            $pathinfo = pathinfo($post->image);
            Storage::disk('public')->delete('/posts/'.$pathinfo['filename'].'.'.$pathinfo['extension']);
        }
        if(!$post)
        {
            return response([
                'message'=>'post not found.'
            ], 403);
        }
        if($post->user_id != auth()->user()->id)
        {
            return response([
                'message'=>'permission deny'
            ],403);
        }
        $post->delete();
        return response([
            'meesage'=>'Post deleted',

        ],200);
    }
}
