<?php

// JS・CSSファイルを読み込む
function add_files()
{
	// キャッシュバージョン：開発中はWP_DEBUG=trueで都度更新
	$cache = (WP_DEBUG) ? date('YmdHis') : '1.0';

	// WordPress提供のjQueryを読み込まない
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js', "", $cache, false);

	// SwiperのCSS・JS
	wp_enqueue_style('swiper-css', 'https://unpkg.com/swiper/swiper-bundle.min.css');
	wp_enqueue_script('swiper-js', 'https://unpkg.com/swiper/swiper-bundle.min.js', array(), false, true);

	// サイト共通のCSS・JS
	wp_enqueue_style('style', get_template_directory_uri() . '/css/style.css', "", $cache);
	wp_enqueue_script('main-script', get_template_directory_uri() . '/js/script.js', array('jquery', 'swiper-js'), $cache, true);
}
add_action('wp_enqueue_scripts', 'add_files');


// アイキャッチ画像の有効化
add_theme_support('post-thumbnails');


// 「news」「catalog」投稿のURLを「/news/ID」「/catalog/ID」に変更
function custom_post_link_id_only($post_link, $post)
{
	if ($post->post_type === 'news') {
		return home_url('/news/' . $post->ID);
	} elseif ($post->post_type === 'catalog') {
		return home_url('/catalog/' . $post->ID);
	}
	return $post_link;
}
add_filter('post_type_link', 'custom_post_link_id_only', 10, 2);


// 上記パーマリンクに対応するリライトルールを追加
function custom_rewrite_rules()
{
	add_rewrite_rule(
		'^news/([0-9]+)/?$',
		'index.php?post_type=news&p=$matches[1]',
		'top'
	);
	add_rewrite_rule(
		'^catalog/([0-9]+)/?$',
		'index.php?post_type=catalog&p=$matches[1]',
		'top'
	);
}
add_action('init', 'custom_rewrite_rules');


// 「catalog」カスタム投稿タイプの登録（未登録の場合）
function register_catalog_post_type()
{
	register_post_type('catalog', array(
		'label' => 'カタログ',
		'public' => true,
		'has_archive' => true,
		'supports' => array('title', 'editor', 'thumbnail'),
		'show_in_rest' => true,
		'rewrite' => false // パーマリンクを手動で指定するため
	));
}
add_action('init', 'register_catalog_post_type');
