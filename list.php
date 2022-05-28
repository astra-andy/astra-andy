<?
@include_once ("connect.php");

$checked = (int)$_GET[checked];
if ($checked>0) {
	// Включить / Выключить юзера
	$user = $db->getRow('select * from Users where User_ID=?s', $checked);
	if ($user) {
		$c = ($user[Checked] ? 0 : 1);
		$db->query('UPDATE Users SET Checked=?s where User_ID=?s', $c, $user['User_ID']);
	}
}

$users = $db->getAll('select * from Users order by Created');


$title = 'Список пользователей';
@include_once ("_header.php");
?>

	<div style='text-align:center;'>
		<h3>Список пользователей</h3>
	</div>

	<style>
	TABLE { width:100%; margin:30px 0; }
	TABLE TD, TABLE TH { padding:10px 0; border-bottom:1px solid #aaa; vertical-align:top }
	TABLE TH { text-align:left; color:#888 }
	</style>

	<table>
	  <tr>
		<th>Ф.И.О.</hr>
		<th>Контакты</hr>
		<th>Паспорт</hr>
		<th>Видимость</hr>
	  </tr>

	  <? foreach ($users as $u) { ?>
	  <tr style='color:<?= ($u[Checked] ? '#000' : '#aaa') ?>'>
			<td><?= $u[Name].'<br><span style="color:#aaa">'.substr($u[Created], 0, 10).'</span>' ?></td>
			<td><?= $u[Email].'<br>'.$u[Phone] ?></td>
			<td><?= $u[Pasport1].'<br>'.$u[Pasport2] ?></td>
			<td>
				<a href="list.php?rnd=<?= rand(0,999) ?>&checked=<?= $u['User_ID'] ?>">
				<?= ($u[Checked] ? 'Активен' : 'Выключен') ?>
				</a>
			</td>
	  </tr>
	  <? } ?>
	</table>

	<div style='text-align:center;'>
	<a href='./' class=but>На главную</a>
	</div>

<?
@include_once ("_footer.php");

?>