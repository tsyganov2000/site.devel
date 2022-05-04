<?php 

$I = new AcceptanceTester($scenario);
$I->amOnPage('/');
$I->click('Войти');
$I->fillField(['id' => 'login_main_login'], 'head@user.com');
$I->fillField(['id' => 'psw_main_login'], 'qweasd');
$I->click('form[name=main_login_form] button[type=submit]');
$I->click('Отделы');
$I->see('first dep');
$I->click('first dep');
$I->see('Customer Customer');
$I->see('Random User');
$I->click('Мой профиль');
$I->click('Выйти');
$I->see('Войти');
$I->makeHtmlSnapshot();



