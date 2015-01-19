<?php

define('VIEW_ROOT', __DIR__.'/view');

$check_login = function () {
	if (!user_id()) {
		echo_json(1, 'user not login');
		return false;
	}
};

run([
	['GET', '^/$', function() {
		$items = Service()['db']->queryAll('SELECT * from item join user on user.id=item.user_id limit 100');
		render(VIEW_ROOT.'/index.html', compact('items'), VIEW_ROOT.'/layout.html');
	}],
	['GET', '^/t/(\d+)$', function ($params) {
		$id = $params[1];
		$db = Service()['db'];
		$item = $db->queryRow('SELECT * from item join user on user.id=item.user_id limit 1');
		$comments = $db->queryAll('SELECT * from comment where item_id=? limit 100', [$id]);
		render(VIEW_ROOT.'/item.html', compact('item', 'comment'), VIEW_ROOT.'/layout.html');
	}],
	['POST', '^/t/$', function () {
		$user_id = user_id();
		$title = _post('title');
		if (empty($title)) {
			echo_json(2, 'title empty');
			exit;
		}
		$text = _post('text');
		$data = compact('user_id', 'title', 'text');
		$db = Service()['db'];
		$id = $db->insert('item', $data);
		echo_json(['id' => $id], 'item created');
	}, $check_login],
	['POST', '^/t/comment/$', function () {
		$user_id = user_id();
		$item_id = _post('item_id');
		if (empty($item_id)) {
			echo_json(2, 'item_id empty');
			exit;
		}
		$text = _post('text');
		if (empty($text)) {
			echo_json(2, 'text empty');
			exit;
		}
		$data = compact('user_id', 'title', 'text');
		$db = Service()['db'];
		$id = $db->insert('comment', $data);
		echo_json(['id' => $id], 'comment created');
	}, $check_login],
	['POST', '^/user/$', function () {
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
		$data = compact('name', 'password');
		$db = Service()['db'];
		$id = $db->insert('user', $data);
		echo_json(['id' => $id], 'user created');
	}],
	['GET', '^/login$', function () {
		render(VIEW_ROOT.'/login.html', VIEW_ROOT.'/layout.html');
 	}],
	['POST', '^/login$', function () {
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
		$db = Service()['db'];
		$user = $db->queryRow('SELECT * from user where name=? and password=? limit 1', [$name, md5($password)]);
		if (empty($user)) {
			echo_json(3, 'password not correct');
		}
		user_id($user['id']);
		redirect('/');
	}],
]);
