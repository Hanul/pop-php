<?php
$contextPath = '../..';
include "$contextPath/lib/pop.php";
include "$contextPath/inc/mysql-config.php";

if ($method->post) {
	
	$validate
	-> target('username')
	-> notNull('아이디를 입력해 주십시오.')
	-> size(3, 20, '아이디는 3자 이상, 20자 이하로 입력해 주십시오.');
	
	$validate
	-> target('password')
	-> notNull('비밀번호를 입력해 주십시오.')
	-> size(6, 20, '비밀번호는 6자 이상, 20자 이하로 입력해 주십시오.')
	-> validate($param->password == $param->confirmPassword, '비밀번호와 비밀번호 확인이 다릅니다.');
	
	$validate
	-> target('confirmPassword')
	-> notNull('비밀번호 확인을 입력해 주십시오.')
	-> size(6, 20, '비밀번호 확인은 6자 이상, 20자 이하로 입력해 주십시오.');
	
	$validate
	-> target('email')
	-> notNull('이메일을 입력해 주십시오.')
	-> email('이메일 형식이 다릅니다.')
	-> size(5, 320, '이메일은 320자 이하로 입력해 주십시오.');
	
	$validate
	-> target('age')
	-> notNull('나이를 입력해 주십시오.')
	-> range(1, 200, '나이는 1살부터 200살까지 입니다.');
	
	if ($validate->ok) {
		if ($isUpdate) {
			// update process
			$mysql->exe("
				UPDATE user_info set
					username = '$param->username',
					password = '$param->password',
					email = '$param->email',
					age = '$param->age'
				WHERE username = '$param->username';
			");
		} else {
			// join process
			$mysql->exe("
				INSERT INTO user_info ( username, password, email, age )
				VALUES ( '$param->username', '$param->password', '$param->email', '$param->age' );
			");
		}
		redirect("$contextPath/showcase/user/view.php?username=$param->username");
	}
}

if ($method->get && $param->username != null) {
	$isUpdate = true;

	$result = $mysql->one("SELECT * FROM user_info WHERE username = '$param->username';");
	
	$param->email = $result->email;
	$param->age = $result->age;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="<?=$contextPath?>/showcase/user/css/form.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<form method="post">
			<?php isUpdate() ?>
			<table>
				<?php if ($validate->no) { ?>
				<tr>
					<td colspan="2">
						<ul class="error">
							<?=$validate->msg('<li>', 'username', '</li>') ?>
							<?=$validate->msg('<li>', 'password', '</li>') ?>
							<?=$validate->msg('<li>', 'confirmPassword', '</li>') ?>
							<?=$validate->msg('<li>', 'email', '</li>') ?>
							<?=$validate->msg('<li>', 'age', '</li>') ?>
						</ul>
					</td>
				</tr>
				<?php } ?>
				<?php if ($isUpdate) { ?>
				<tr>
					<th>아이디</th>
					<td>
						<?=$param->username ?>
						<input type="hidden" name="username" value="<?=$param->username ?>">
					</td>
				</tr>
				<?php } else { ?>
				<?=$validate->msg('<tr>
					<td colspan="2" class="error">
						', 'username', '
					</td>
				</tr>') ?>
				<tr>
					<th>아이디</th>
					<td><input type="text" name="username" value="<?=$param->username ?>"></td>
				</tr>
				<?php } ?>
				<?=$validate->msg('<tr>
					<td colspan="2" class="error">
						', 'password', '
					</td>
				</tr>') ?>
				<tr>
					<th><?php if ($isUpdate) echo '새 ' ?>비밀번호</th>
					<td><input type="password" name="password"></td>
				</tr>
				<?=$validate->msg('<tr>
					<td colspan="2" class="error">
						', 'confirmPassword', '
					</td>
				</tr>') ?>
				<tr>
					<th><?php if ($isUpdate) echo '새 ' ?>비밀번호 확인</th>
					<td><input type="password" name="confirmPassword"></td>
				</tr>
				<?=$validate->msg('<tr>
					<td colspan="2" class="error">
						', 'email', '
					</td>
				</tr>') ?>
				<tr>
					<th>이메일</th>
					<td><input type="text" name="email" value="<?=$param->email ?>"></td>
				</tr>
				<?=$validate->msg('<tr>
					<td colspan="2" class="error">
						', 'age', '
					</td>
				</tr>') ?>
				<tr>
					<th>나이</th>
					<td><input type="text" name="age" value="<?=$param->age ?>"></td>
				</tr>
				<tr>
					<?php if ($isUpdate) { ?>
					<td colspan="2" class="submit"><input type="submit" value="회원 정보 수정"></td>
					<?php } else { ?>
					<td colspan="2" class="submit"><input type="submit" value="회원 가입"></td>
					<?php } ?>
				</tr>
			</table>
		</form>
	</body>
</html>