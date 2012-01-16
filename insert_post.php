<?php
/*
Plugin Name: Insert Post
Plugin URI: http://key-jam.com/
Description: wp_insert_postラップ
Version: 0.1
Author: E.Kazuki(keyjam)
Author URI: http://key-jam.com/
Bug Report: E.Kazuki
*/

/*  Copyright 2011 keyjam 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// 投稿widget内でショートコードを有効にする
add_filter( 'widget_text', 'do_shortcode' );

// スタイルシートのパスを指定する
wp_register_style( 'insrt_post', plugins_url( basename( __FILE__, '.php' ), dirname( __FILE__ )) . '/css/style.css', false, false, 'all' );

// フロントページの場合キューにスタイルシートを登録する
if ( !is_admin() ) wp_enqueue_style( 'insrt_post' );

// ショートコードを登録する
add_shortcode( 'insrt_post', 'insertPostJa' );

/******************************************************************************
 * insertPostJa
 * 
 * @author E.Kazuki
 * @version	0.1
 * 
 */
function insertPostJa() {

	// 初期表示
	if ( empty( $_POST['confirm'] ) ) : return form();

	else : 

		// ERRORチェック
		if ( $error = errorCheck( $_POST ) ) return $error;

		// 登録
		return comfirm( $_POST );

	endif;

}

/**
 * 入力フォーム
 * 
 * @param none
 * @return String html
 */
function form () {

	$pullDown = wp_dropdown_categories( array( 'hide_empty' => 0,'echo' => 0,'show_count' => 0,'hierarchical' => 1,'name' => 'category') );

	$data = <<< HTML
	 <form action="#mark" method="post">
		<input type="hidden" name="confirm" value="1" />
		<dl>
			<dt>Title</dt>
			<dd><input type="text" name="title" size="40" /></dd>
			<dt>Category</dt>
			<dd>{$pullDown}</dd>
			<dt>Content</dt>
			<dd><textarea name="content" rows="7" cols="70"></textarea></dd>
		</dl>
		<input type="submit" value="Publish" />
	</form>
HTML;

	return $data;

}

/**
 * 完了フォーム
 * 
 * @param array POST data
 * @return String
 */
function comfirm ( $request ) {

	$post_args = array(
		'post_title' => apply_filters( 'the_title', $_POST['title'] ), 
		'post_content' => apply_filters( 'the_content', $_POST['content'] ), 
		'post_status' => 'pending', 
		'post_author' => 1, 
		'post_category' => array( $_POST['category'] ) 
	);

	if ( wp_insert_post( $post_args ) ) return '<div id="mark">投稿が完了しました<br/><a href="' . get_permalink() . '">投稿する</a></div>';

}

/**
 * ERRORチェック
 * 
 * @param array POST data
 * @return string error
 */
function errorCheck ( $request ) {

	if ( empty( $request['title'] ) ) :

		$data .= '<div id="wpip-message"><span class="error">Titleが未入力です</span><br/><a href="javascript:history.back()">&lsaquo; back</a></div>';

	elseif ( empty( $request['content'] ) ) :

		$data .= '<div id="wpip-message"></span class="error">Contentが未入力です</span><br/><a href="javascript:history.back()">&lsaquo; back</a></div>';

	endif;

	return $data;
}

?>