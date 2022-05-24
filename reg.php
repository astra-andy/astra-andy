<?
include_once ("connect.php");

$er = '';
$result = 0;

$Action = $_POST['Action'];
$Name = trim($_POST['Name']);
$Email = trim(mb_strtolower($_POST['Email']));
$Phone = trim($_POST['Phone']);
$Pasport1 = trim($_POST['Pasport1']);
$Pasport1 = preg_replace('/[^0-9]/', '', $_POST['Pasport1']);
$Pasport2 = trim($_POST['Pasport2']);
$Pass = trim($_POST['Pass']);
$Pass2 = trim($_POST['Pass2']);

if ($Action=='reg') {
	// РЕГИСТРАЦИЯ

	// Проверка полей
	if (!$er && !$Name) $er = 'Ошибка! Необходимо ввести Ваше Ф.И.О.';
	if (!$er && !$Email) $er = 'Ошибка! Необходимо ввести e-mail адрес.';
	if (!$er && $Email && !preg_match('/^[-a-z0-9._]{2,32}@[-a-z0-9.]{2,32}\.[a-z]{2,6}$/i', $Email)) $er = 'Ошибка! Проверьте правильность написания e-mail адреса.';
	if (!$er && !$Phone) $er = 'Ошибка! Необходимо обязательно ввести номер телефона.';
	if (!$er && $Phone && !preg_match('/[0-9][0-9]/', $Phone)) $er = 'Ошибка! Номер телефона должен содержать цифры.';
	if (!$er && !$Pasport1) $er = 'Ошибка! Необходимо обязательно ввести номер паспорта.';
	if (!$er && strlen($Pasport1)<10) $er = 'Ошибка! Номер паспорта должен содержать 10 цифр.';
	if (!$er && !$Pasport2) $er = 'Ошибка! Необходимо обязательно ввести - кем выдан паспорт.';
	if (!$er && !$Pass) $er = 'Ошибка! Необходимо ввести пароль.';
	if (!$er && $Pass && strlen($Pass)<6) $er = 'Ошибка! Пароль должен содержать минимум 6 символов.';
	if (!$er && !$Pass2) $er = 'Ошибка! Необходимо ввести пароль повторно.';
	if (!$er && $Pass != $Pass2) $er = 'Ошибка! Введённый дважды пароль не совпадает.<br>Проверьте правильность ввода.';

	if (!$er) {
		// Все поля заполнены.

		// Сохраним e-mail в Cookie на 10 дней
		setcookie('user_login', $Email, time()+3600*24*10, '/');

		if ($db->getOne('select User_ID from Users where Email=?s limit 1', $Email)) {
			$er = 'Такой e-mail адрес уже зарегистрирован.<br>Необходимо ввести другой адрес.';
		} else {
			// Регистрируем

			$result = 1;

			$p = MD5($Pass);  // Пароль

			$db->query('INSERT INTO Users SET Name=?s, Email=?s, Phone=?s, Password=?s, Pasport1=?s, Pasport2=?s, Created=NOW(), Checked=1', $Name, $Email, $Phone, $p, $Pasport1, $Pasport2);
		}
	}
}


$title = 'Регистрация';
@include_once ("_header.php");
?>

<div style='text-align:center'>

	<? if ($result==1) { ?>

		<h3>Регистрация завершена</h3>

		<br>
		<a href='./' class='but'>Авторизоваться</a><br><br>

	<? } else { ?>

		<h3>Регистрация</h3>

		<div style='padding:10px 0;'>
		<?= ($er ? '<div class="error">'.$er.'</div>' : '<span style="color:#888">Все поля обязательны для заполнения.</span>') ?>
		</div>

		<form action='reg.php' method='POST' class='auth'>
		<input type='hidden' name='Action' value='reg'>

		<input type='text' name='Name' placeholder='Ваше Ф.И.О.' size=20 value="<?= htmlspecialchars($Name) ?>"><br>
		<input type='text' name='Email' placeholder='E-mail' size=20 value="<?= htmlspecialchars($Email) ?>"><br>
		<input type='text' name='Phone' placeholder='Номер телефона' size=20 value="<?= htmlspecialchars($Phone) ?>"><br>
		<div style='padding-top:5px'>Паспортные данные:</div>
		<input type='text' name='Pasport1' placeholder='Номер' size=20 value="<?= htmlspecialchars($Pasport1) ?>"><br>
		<input type='text' name='Pasport2' placeholder='Кем выдан' size=20 value="<?= htmlspecialchars($Pasport2) ?>"><br>
		<div style='padding-top:5px'>Придумайте пароль:</div>
		<input type='password' name='Pass' placeholder='' size=20 value="<?= htmlspecialchars($Pass) ?>"><br>
		<input type='password' name='Pass2' placeholder='Повторите ввод пароля' size=20 value="<?= htmlspecialchars($Pass2) ?>"><br>

		<div style='padding-top:10px'>
		<input type='submit' value='Регистрация' class='but'><br>
		</div>

		</form>

	<? } ?>

</div>


<?
@include_once ("_footer.php");

?>