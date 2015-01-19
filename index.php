<?php

define('VIEW_ROOT', __DIR__.'/view');

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
		if (!$user_id) {
			echo_json(1, 'user not login');
			exit;
		}
		$title = _post('title');
		if (empty($title)) {
			echo_json(2, 'title empty');
			exit;
		}
		$text = _post('text');
		$data = compact('user_id', 'title', 'text');
		$db = Service()['db'];
		$id = $db->insert('item', $data);
		echo_json(['id' => $id]);
	}],
	['POST', '^/t/comment/$', function () {
		$user_id = user_id();
		if (!$user_id) {
			echo_json(1, 'user not login');
			exit;
		}
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
		echo_json(['id' => $id]);
	}],
]);
