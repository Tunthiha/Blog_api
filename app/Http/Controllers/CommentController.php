<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Post;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $post = Post::find($id);
        if(!$post)
        {
            return response([
                'message'=>'post not found.'
            ], 403);
        }
        return response([
            'comments'=>$post->comment()->with('user:id,name,image')->get()
        ], 200);
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
    public function store(Request $request,$id)
    {

        $post = Post::find($id);
        if(!$post)
        {
            return response([
                'message'=>'post not found.'
            ], 403);
        }
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        Comment::create([
            'comment' => $attrs['comment'],
            'post_id'=>$id,
            'user_id'=>auth()->user()->id
        ]);
        return response([
            'message'=>'comment created',

        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $comment = Comment::find($id);
        if(!$comment)
        {
            return response([
                'message'=>'comment not found.'
            ], 403);
        }
        if($comment->user_id != auth()->user()->id)
        {
            return response([
                'message'=>'permission deny'
            ],403);
        }
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $attrs['comment'],
        ]);
        return response([
            'message'=>'comment updated',

        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);
        if(!$comment)
        {
            return response([
                'message'=>'comment not found.'
            ], 403);
        }
        if($comment->user_id != auth()->user()->id)
        {
            return response([
                'message'=>'permission deny'
            ],403);
        }
        $comment->delete();

        return response([
            'message'=>'comment delete.'
        ], 200);
    }
}
