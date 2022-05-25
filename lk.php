<?
@include_once ("connect.php");
@include_once ("auth.php");

if ( test_auth() == 0 ) {
	// Не авторизован. Сделаем редирект на главную.
	header ('Location: ./');
	exit;
}

$er = '';

$user = $db->getRow('select * from Users where Email=?s and Checked=1 limit 1', $_COOKIE['user_login']);

if (!$user) $er = 'Ошибка! Пользователь заблокирован или удалён.';


$title = 'Личный кабинет';
@include_once ("_header.php");
?>


	<div style='text-align:center;'>

		<h3>Личный кабинет</h3>

		<? if ($er) { ?>
			<div style="padding:10px 0;" class="error"><?= $er ?></div>
			<br>
		<? } else { ?>

			<br>
			<img src='img/user.png' style='width:20%; min-width:100px' alt=''><br>

			<div style='font-size:140%; line-height:140%'><strong><?= $user[Name] ?></strong>, добро пожаловать!</div>
			<div style='color:#aaa'>Вы зарегистрированы в системе <?= substr($user[Created], 0, 10) ?></div>
			<br>

			<? if ($user[Phone]) { ?>
			<div>Ваш номер телефона: <?= $user[Phone] ?></div>
			<? } ?>

			<? if ($user[Pasport1]) { ?>
			<div>Номер паспорта: <?= $user[Pasport1] ?></div>
			<? } ?>
			<? if ($user[Pasport2]) { ?>
			<div>Паспорт выдан: <?= $user[Pasport2] ?></div>
			<? } ?>
			<br>

			<a href='./edit.php' class=but>Изменить личные данные</a><br>

		<? } ?>

		<a href='./?Action=exit' class=but>Выход</a><br>

	</div>


<?
@include_once ("_footer.php");

?>
