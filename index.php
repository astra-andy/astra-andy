<?
@include_once ("connect.php");
@include_once ("auth.php");

$er = '';
$mes = '';

$Action = $_POST['Action'];

$Email = trim(mb_strtolower($_POST['Email']));
// Если e-mail пустой, то попробуем из куки взять
if (!$Email && $_COOKIE['user_login']) $Email = $_COOKIE['user_login'];

$Pass = trim($_POST['Pass']);

if ($Action=='auth') {
	// Попытка авторизации

	if (!$Email) {
		$er = 'Ошибка! Необходимо ввести e-mail адрес.';
	} else if (!preg_match('/^[-a-z0-9._]{2,32}@[-a-z0-9.]{2,32}\.[a-z]{2,6}$/i', $Email)) {
		$er = 'Ошибка! Проверьте правильность написания e-mail адреса.';
	} else if (!$Pass) {
		$er = 'Ошибка! Необходимо ввести пароль.';
	} else {
		// Вытаскиваем из базы пароль в MD5
                $p = $db->getOne('select Password from Users where Email=?s and Checked=1 limit 1', $Email);

		if (md5($Pass) != $p) {
			$er = 'Ошибка! E-mail или пароль не верны.<br>Попробуйте снова.';
		} else {
			// Авторизация

			// Сгенерим ключ с привязкой ко времени
			$t = (int)(time() / 100);
			$k = substr(md5($p.$Email.$t), 0, 16).'-'.$t;

			setcookie('user_login', $Email, time()+3600*24*10, '/');
			setcookie('user_key', $k, time()+3600*24*5, '/');

			header ('Location: ./lk.php');
			exit;
		}
	}

} else if ($_GET['Action']=='exit') {
	// Выход. Завершение авторизации
	setcookie('user_key', '', 0, '/');
	$mes = 'Вы вышли. Заходите ещё! :)';

} else if ( test_auth() ){
	// Уже авторизован. Сделаем редирект на главную.
	header ('Location: ./lk.php');
	exit;
}



$title = 'Главная';
@include_once ("_header.php");
?>


		<div style='text-align:center'>

		<?= ($mes ? '<div style="padding:40px 0;">'.$mes.'</div>' : '') ?>

		<h3>Авторизация</h3>

		<?= ($er ? '<div style="padding:10px 0;" class="error">'.$er.'</div>' : '') ?>

		<form action='index.php' method='POST' class='auth'>
		<input type='hidden' name='Action' value='auth'>

		<input type='text' name='Email' placeholder='E-mail' size=20 value="<?= htmlspecialchars($Email) ?>"><br>
		<input type='password' name='Pass' placeholder='Пароль' size=20 value=''><br>
		<input type='submit' value='Вход' class=but><br>
		</form>

		<a href='reg.php' class=but>Регистрация</a><br>

		</div>


<?
@include_once ("_footer.php");

?>