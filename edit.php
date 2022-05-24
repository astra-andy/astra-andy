<?
@include_once ("connect.php");
@include_once ("auth.php");

if ( test_auth() == 0 ) {
	// Не авторизован. Сделаем редирект на главную.
	header ('Location: ./');
	exit;
}

$er = '';
$form = 1;
$changePass = 0;

$user = $db->getRow('select * from Users where Email=?s and Checked=1 limit 1', $_COOKIE['user_login']);

if (!$user) {
	$er = 'Ошибка! Пользователь заблокирован или удалён.';
	$form = 0;
}

$Action = $_POST['Action'];

if (!$er && $Action=='save') {
	// Сохранение

	$Name = trim($_POST['Name']);
	$Phone = trim($_POST['Phone']);
	$Pasport1 = trim($_POST['Pasport1']);
	$Pasport1 = preg_replace('/[^0-9]/', '', $_POST['Pasport1']);
	$Pasport2 = trim($_POST['Pasport2']);
	$Pass = trim($_POST['Pass']);
	$Pass2 = trim($_POST['Pass2']);

	// Проверка полей
	if (!$er && !$Name) $er = 'Ошибка! Необходимо ввести Ваше Ф.И.О.';
	if (!$er && !$Phone) $er = 'Ошибка! Необходимо обязательно ввести номер телефона.';
	if (!$er && $Phone && !preg_match('/[0-9][0-9]/', $Phone)) $er = 'Ошибка! Номер телефона должен содержать цифры.';
	if (!$er && !$Pasport1) $er = 'Ошибка! Необходимо обязательно ввести номер паспорта.';
	if (!$er && strlen($Pasport1)<10) $er = 'Ошибка! Номер паспорта должен содержать 10 цифр.';
	if (!$er && !$Pasport2) $er = 'Ошибка! Необходимо обязательно ввести - кем выдан паспорт.';

	if (!$er && $Pass && strlen($Pass)<6) $er = 'Ошибка! Пароль должен содержать минимум 6 символов.';
	if (!$er && $Pass && !$Pass2) $er = 'Ошибка! Необходимо ввести пароль повторно.';
	if (!$er && $Pass && $Pass != $Pass2) $er = 'Ошибка! Введённый дважды пароль не совпадает.<br>Проверьте правильность ввода.';

	if (!$er) {
		$db->query('UPDATE Users SET Name=?s, Phone=?s, Pasport1=?s, Pasport2=?s where User_ID=?s', $Name, $Phone, $Pasport1, $Pasport2, $user['User_ID']);

		if ($Pass && $Pass2) {
			// Смена пароля
			$p = MD5($Pass);  // Пароль

			$db->query('UPDATE Users SET Password=?s where User_ID=?s', $p, $user['User_ID']);

			setcookie('user_key', '', 0, '/');
			$changePass = 1;
		}

		$form = 0;
	}

} else if (!$er) {
	// Редактирование
	$Name = $user[Name];
	$Phone = $user[Phone];
	$Pasport1 = $user[Pasport1];
	$Pasport2 = $user[Pasport2];
}


$title = 'Личный кабинет';
@include_once ("_header.php");
?>


<div style='text-align:center'>

	<h3>Редактирование личных данных</h3>

	<?= ($er ? '<div style="padding:10px 0;" class="error">'.$er.'</div>' : '') ?>

	<? if ($form==1) { ?>

		<form action='edit.php' method='POST' class='auth'>
		<input type='hidden' name='Action' value='save'>

		<input type='text' name='Name' placeholder='Ваше Ф.И.О.' size=20 value="<?= htmlspecialchars($Name) ?>"><br>
		<input type='text' name='Phone' placeholder='Номер телефона' size=20 value="<?= htmlspecialchars($Phone) ?>"><br>

		<div style='padding-top:5px'>Паспортные данные:</div>
		<input type='text' name='Pasport1' placeholder='Номер' size=20 value="<?= htmlspecialchars($Pasport1) ?>"><br>
		<input type='text' name='Pasport2' placeholder='Кем выдан' size=20 value="<?= htmlspecialchars($Pasport2) ?>"><br>

		<div style='padding-top:5px'>Новый пароль, если требуется сменить:</div>
		<input type='password' name='Pass' placeholder='' size=20 value="<?= htmlspecialchars($Pass) ?>"><br>
		<input type='password' name='Pass2' placeholder='Повторите ввод пароля' size=20 value="<?= htmlspecialchars($Pass2) ?>"><br>

		<div style='padding-top:10px'>
		<input type='submit' value='Сохранить' class='but'><br>
		</div>

		</form>

		<a href='./lk.php' class=but>Отмена</a><br>

	<? } else { ?>

		<br>
		<h3 style='color:#08b'>Изменения сохранены</h3>
		<br>
		<? if ($changePass==1) { ?>
			<a href='./' class=but>Авторизоваться с новым паролем</a><br>
		<? } else { ?>
			<a href='./lk.php' class=but>В личный кабинет</a><br>
		<? } ?>

	<? } ?>

</div>


<?
@include_once ("_footer.php");

?>