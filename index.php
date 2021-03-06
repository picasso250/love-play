<?php

define('VIEW_ROOT', __DIR__.'/view');

require __DIR__.'/php-tiny-frame/autoload.php';


$check_login = function () {
	if (!user_id()) {
		echo_json(1, 'user not login');
		return false;
	}
};

session_start();
Service('db', new DB('mysql:dbname=love_play', 'root', 'root'));

run([
	['GET', '%^/$%', function() {
		$fields = 'item.id, name as username, title, `text`, item.create_time';
		$sql = "SELECT $fields from item join user on user.id=item.user_id order by item.id desc limit 100";
		$items = Service('db')->queryAll($sql);
		render(VIEW_ROOT.'/index.html', compact('items'), VIEW_ROOT.'/layout.html');
	}],
	['GET', '%^/t/(\d+)$%', function ($params) {
		$id = $params[1];
		$db = Service('db');
		$fields = 'item.id, name as username, title, `text`, item.create_time';
		$item = $db->queryRow("SELECT $fields from item join user on user.id=item.user_id limit 1");
		$sql = 'SELECT user.name as username, c.* from comment c join user on user.id=c.user_id where item_id=? limit 100';
		$comments = $db->queryAll($sql, [$id]);
		render(VIEW_ROOT.'/item.html', compact('item', 'comments'), VIEW_ROOT.'/layout.html');
	}],
	['POST', '%^/t/$%', function () {
		$user_id = user_id();
		$title = _post('title');
		if (empty($title)) {
			echo_json(2, 'title empty');
			exit;
		}
		$text = _post('text');
		$data = compact('user_id', 'title', 'text');
		$db = Service('db');
		$id = $db->insert('item', $data);
		echo_json(['id' => $id], 'item created');
	}, $check_login],
	['POST', '%^/t/(\d+)/comment/$%', function ($params) {
		$user_id = user_id();
		$item_id = $params[1];
		$text = _post('text');
		if (empty($text)) {
			echo_json(2, 'text empty');
			exit;
		}
		$data = compact('user_id', 'item_id', 'title', 'text');
		$db = Service('db');
		$id = $db->insert('comment', $data);
		echo_json(['id' => $id], 'comment created');
	}, $check_login],
	['POST', '%^/user/$%', function () {
		$name = _post('name');
		if (empty($name)) {
			echo_json(2, 'name empty');
			exit;
		}
		$db = Service('db');
		if ($db->queryRow('SELECT `id` FROM `user` WHERE `name`=? LIMIT 1', [$name])) {
			echo_json(4, 'user exists');
			exit;
		}
		$password = _post('password');
		if (empty($password)) {
			echo_json(2, 'password empty');
			exit;
		}
		$data = compact('name', 'password');
		$id = $db->insert('user', [
			'name' => $name,
			'password' => md5($password),
		]);
		echo_json(['user_id' => $id], 'user created');
	}],
	['GET', '%^/login$%', function () {
		render(VIEW_ROOT.'/login.html', [], VIEW_ROOT.'/layout.html');
 	}],
	['POST', '%^/login$%', function () {
		$name = _post('name');
		if (empty($name)) {
			echo_json(2, 'name empty');
			exit;
		}
		$password = _post('password');
		if (empty($password)) {
			echo_json(2, 'password empty');
			exit;
		}
		$db = Service('db');
		$user = $db->queryRow('SELECT * from user where name=? and password=? limit 1', [$name, md5($password)]);
		if (empty($user)) {
			echo_json(3, 'password not correct');
			exit;
		}
		user_id($user['id']);
		echo_json($user);
	}],
	['GET', '%^/logout$%', function () {
		user_id(0);
		redirect('/');
 	}],
]);

function user_id($user_id = null)
{
	$key = 'xxuserid';
	if (func_num_args() === 0) {
		return isset($_SESSION[$key]) ? $_SESSION[$key] : 0;
	} else {
		$_SESSION[$key] = $user_id;
	}
}
