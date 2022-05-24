<?
// Проверка авторизации

function test_auth() {
	global $db;

	$Email = $_COOKIE['user_login'];
	$k = $_COOKIE['user_key'];

	if ($Email && $k) {
		$res = 1;
		$tmp = explode('-', $k);
		if ($tmp[1]*100-100 > time() || $tmp[1]*100+3600*24 < time()) {
			// Время жизни ключа истекло
		} else {
        	        $p = $db->getOne('select Password from Users where Email=?s and Checked=1 limit 1', $Email);
			if (!$p) {
				// Не найден юзер
			} else if (substr(md5($p.$Email.$tmp[1]), 0, 16) != $tmp[0]) {
				// Ключ повреждён
			} else {
				$res = 0;
			}
		}

		if ($res) {
			// Ключ обнуляем и редирект на главную
			setcookie('user_key', '', 0, '/');
			header ('Location: ./?rnd='.rand(0,9999));
			exit;
		} else {
			return 1;  // Авторизован
		}
	}
	return 0;  // НЕ авторизован
}
?>